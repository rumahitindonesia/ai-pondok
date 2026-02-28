<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Santri;
use Illuminate\Support\Facades\DB;

class ImportSantriData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'santri:import {file : Path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Santri data from a CSV file and auto-generate NIS based on angkatan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->error("File not found or not readable: {$filePath}");
            return 1;
        }

        $header = null;
        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = array_map('trim', array_map('strtolower', $row));
                } else {
                    if (count($header) == count($row)) {
                        $data[] = array_combine($header, $row);
                    } else {
                        $this->warn("Row skipped due to mismatched column count: " . implode(',', $row));
                    }
                }
            }
            fclose($handle);
        }

        $requiredColumns = ['nama', 'entitas', 'angkatan', 'jenis_kelamin', 'kelas'];
        foreach ($requiredColumns as $col) {
            if (!in_array($col, $header)) {
                $this->error("Missing required column in CSV header: {$col}");
                return 1;
            }
        }

        $this->info("Found " . count($data) . " rows to process.");
        $successCount = 0;

        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                $nama = trim($row['nama']);
                $entitas = trim($row['entitas']);
                $angkatan = trim($row['angkatan']);
                $jenis_kelamin = trim($row['jenis_kelamin']);
                $kelas = trim($row['kelas']);

                if (empty($nama) || empty($angkatan)) {
                    $this->warn("Row " . ($index + 2) . " skipped: 'nama' and 'angkatan' are required.");
                    continue;
                }

                // Format angkatan as 3 digits (e.g., "1" -> "001")
                $angkatanPrefix = str_pad((int)$angkatan, 3, '0', STR_PAD_LEFT);

                // Find the latest NIS for this angkatan to determine the next sequence
                $latestSantri = Santri::where('nis', 'like', $angkatanPrefix . '%')
                    ->orderByRaw('CAST(SUBSTRING(nis, 4) AS UNSIGNED) DESC')
                    ->first();

                $nextSequence = 1;
                if ($latestSantri && strlen($latestSantri->nis) > 3) {
                    $lastSequence = (int)substr($latestSantri->nis, 3);
                    $nextSequence = $lastSequence + 1;
                }

                // Format sequence as 3 digits (e.g., "1" -> "001")
                $sequenceString = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
                $generatedNis = $angkatanPrefix . $sequenceString;

                Santri::create([
                    'nama' => $nama,
                    'entitas' => $entitas,
                    'angkatan' => (int)$angkatan,
                    'jenis_kelamin' => strtoupper($jenis_kelamin) === 'P' ? 'P' : 'L',
                    'kelas' => $kelas,
                    'nis' => $generatedNis,
                    'status' => 'Santri Aktif',
                ]);

                $successCount++;
            }
            DB::commit();
            $this->info("Successfully imported {$successCount} Santri records with auto-generated NIS.");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
}
