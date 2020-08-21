export default function(element, eventName, detailObject) {
    
    element.dispatchEvent(new CustomEvent(eventName, {
        detail: detailObject || {},
        bubbles: true,
    }));

    return this;
}