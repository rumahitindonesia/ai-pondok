import { ref } from 'vue';

export const notifications = ref([]);

export const notify = (message, type = 'success', duration = 3000) => {
    const id = Date.now();
    notifications.value.push({ id, message, type, duration });
};

export const removeNotification = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id);
};
