const getXSRFTokenFromCookie = () =>
    decodeURIComponent(
        document.cookie
            .split("; ")
            .find((row) => row.startsWith("XSRF-TOKEN" + "="))
            ?.split("=")[1],
    );

/**
 * @param {string} endpoint
 * @param {import("./types.js").fetchJSONOptions} options
 */
const fetchJSON = async (endpoint, options = {}) => {
    const {
        method = "GET",
        body,
        headers = {},
        showSuccess = false,
        showError = true,
    } = options;

    const fetchOptions = {
        method,
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            "X-XSRF-TOKEN": getXSRFTokenFromCookie(),
            ...headers,
        },
    };

    if (body) {
        if (body instanceof FormData) {
            fetchOptions.body = body;
        } else {
            fetchOptions.body = JSON.stringify(body);
            fetchOptions.headers["Content-Type"] = "application/json";
        }
    }

    let ok = false;
    let message = "";
    let errors = {};
    let data;

    try {
        const response = await fetch(`/api${endpoint}`, fetchOptions);
        ok = response.ok;

        const json = await response.json();

        if (!ok) {
            message =
                json.message ||
                `HTTP 请求出错：${response.status} - ${response.statusText}`;
            errors = json.errors || {};
            if (showError) {
                alert(message);
            }
        } else {
            data = json.data;
            if (showSuccess) {
                alert("操作成功");
            }
        }
    } catch (error) {
        message = error.message;
        if (showError) {
            alert(message);
        }
    }

    return { ok, data, message, errors };
};

export default fetchJSON;
