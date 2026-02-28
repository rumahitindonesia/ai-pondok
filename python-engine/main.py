from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import random

app = FastAPI()

# Allow CORS for development
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/")
def read_root():
    return {"message": "ERP Pondok Pesantren - Python Engine Active"}

@app.get("/api/user-stats")
def get_user_stats():
    # Mock data for Oracle JET charts
    return {
        "total_santri": random.randint(500, 1000),
        "active_santri": random.randint(400, 500),
        "new_registrations": random.randint(10, 50),
        "monthly_revenue": [
            {"month": "Jan", "value": random.randint(1000, 5000)},
            {"month": "Feb", "value": random.randint(1000, 5000)},
            {"month": "Mar", "value": random.randint(1000, 5000)},
            {"month": "Apr", "value": random.randint(1000, 5000)},
        ]
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8005)
