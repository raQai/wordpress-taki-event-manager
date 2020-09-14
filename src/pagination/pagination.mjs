const getPagesArray = (numPages) =>
    !numPages || numPages < 1
        ? []
        : Array.from(Array(parseInt(numPages)), (_, i) => i + 1),
    middleValue = (a, b, c) =>
        a + b + c - Math.min(a, b, c) - Math.max(a, b, c);

export const getPages = (total, active, maxItems) => {
    const pagesArray = getPagesArray(total);
    if (total <= maxItems) return pagesArray;
    const preferredNumPagesBoundary = (maxItems - 1) >> 1, // remove first and last element and the actual element and divide by 2
        leftUpperBoundary = active - total + maxItems - 1,
        leftLowerBoundary = active - 1,
        rightUpperBoundary = maxItems - active,
        rightLowerBoundary = total - active,
        leftBoundary =
            active -
            middleValue(
                preferredNumPagesBoundary,
                leftUpperBoundary,
                leftLowerBoundary
            ),
        rightBoundary =
            active +
            middleValue(
                preferredNumPagesBoundary - (maxItems % 2) + 1,
                rightUpperBoundary,
                rightLowerBoundary
            );

    const filtered = pagesArray.filter(
        (k) => k === 1 || k === total || (k > leftBoundary && k < rightBoundary)
    );
    if (active > preferredNumPagesBoundary + 1) {
        filtered[1] = "...";
    }
    if (active < total - preferredNumPagesBoundary) {
        filtered[filtered.length - 2] = "...";
    }
    return filtered;
};
