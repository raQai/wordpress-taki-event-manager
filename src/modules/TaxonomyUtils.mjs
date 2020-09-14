const apiUrl = __eventsApp.env.API_URL;
const getRequestUrl = (taxonomy) => taxonomy && `${apiUrl}${taxonomy}`;

export const serializeValue = (value) => {
    if (!value) {
        return;
    }
    if (Array.isArray(value)) {
        return value.join(",");
    }
    return value;
};

export default {
    fetch: (taxonomy) =>
        fetch(getRequestUrl(taxonomy), {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    return Promise.reject(Error(response.statusText));
                }
                return response;
            })
            .then((response) => response.json()),

    asQueryParams: (taxonomies) => {
        const params = [];
        if (!(taxonomies instanceof Object)) {
            return params;
        }

        Object.keys(taxonomies)
            .filter((taxonomy) => taxonomies[taxonomy])
            .forEach((taxonomy) => {
                let param = serializeValue(taxonomies[taxonomy]);
                if (param) {
                    params[taxonomy] = param;
                }
            });
        return params;
    }
}