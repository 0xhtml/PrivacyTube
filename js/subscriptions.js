export function getSubscriptions() {
    return JSON.parse(localStorage.getItem("subscriptions")) || [];
}

export function setSubscriptions(subscriptions) {
    localStorage.setItem("subscriptions", JSON.stringify(subscriptions));
}
