export default class Cache {
    cacheObj = {};

    put(key, obj) {
        if (!key || !obj) {
            return;
        }
        this.cacheObj[key] = JSON.stringify(obj);
    };
    hasChanged(key, obj) {
        return JSON.stringify(obj) !== this.cacheObj[key];
    }
}