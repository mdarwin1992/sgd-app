export default {
    async setAuthenticate(url, request) {
        try {
            this.toggleLoadingBackground(true);
            const response = await fetch(url, {
                method: "POST",
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify(request)
            });
            const responseData = await response.json();
            response.ok ? this.handleSuccess(responseData) : this.handleError(responseData.message);
        } catch (error) {
            this.handleError(error.message);
        } finally {
            this.toggleLoadingBackground(false);
        }
    },

    handleSuccess(data) {
        const expirationTime = Date.now() + 3600000; // 1 hora en milisegundos
        const storeData = (storage) => {
            Object.entries(data).forEach(([key, value]) => {
                storage.setItem(key, typeof value === 'object' ? JSON.stringify(value) : value);
            });
            // Convertimos expirationTime a string antes de almacenarlo
            storage.setItem('expirationTime', expirationTime.toString());
        };

        storeData(localStorage);
        storeData(this); // Esto llamará a setItem que a su vez usará setCookie

        setTimeout(() => location.href = '/dashboard', 100);
    },

    handleError(message) {
        this.showMessage('danger', message);
    },

    showMessage(type, message) {
        document.getElementById('messages').innerHTML = `
            <div class="alert alert-${type} alert-rounded">
                ${message}
            </div>`;
    },

    toggleLoadingBackground(show) {
        document.getElementById('loading-background').style.display = show ? 'flex' : 'none';
    },

    setItem(name, value) {
        this.setCookie(name, value, 1);
    },

    setCookie(name, value, days) {
        const expires = days ? `; expires=${new Date(Date.now() + days * 86400000).toUTCString()}` : '';
        document.cookie = `${name}=${value || ''}${expires}; path=/`;
    }
};
