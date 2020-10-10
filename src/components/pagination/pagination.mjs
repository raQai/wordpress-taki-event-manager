/**
 * Creates an array of numbers from 1 to numPages.
 * 
 * @param {number} numPages The number of pages.
 * 
 * @return {number[]} An array containing values from 1 to numPages
 */
const getPagesArray = (numPages) =>
    !numPages || numPages < 1
        ? []
        : Array.from(Array(parseInt(numPages)), (_, i) => i + 1);

/**
 * Determines which of the given values is the middle value.
 * 
 * @param {number} a Value a.
 * @param {number} b Value b.
 * @param {number} c Value c.
 * 
 * @return {number} The middle value of the provided 3.
 */
const getMiddleValue = (a, b, c) =>
    a + b + c - Math.min(a, b, c) - Math.max(a, b, c);

/**
 * Creates an array of pages to be displayed for the full pagination.
 *
 * Replaces the second and second to last value with "..." if the number of
 * pages exceeds the given maxItems. In this case, only the values next to
 * the given active page will be shown.
 * 
 * This will also take into account if "..." is necessary or not, hence not
 * replacing pages if it is not needed.
 * 
 * examples:
 * - total = 4, active = any, maxItems = 5
 *   1, 2, 3, 4
 * - total = 14, active = 5, maxItems = 5
 *   1, ..., 5, ..., 14
 * - total = 14, active = 5, maxItems = 7
 *   1, ..., 4, 5, 6, ..., 14
 * - total = 14, active = 3, maxItems = 7
 *   1, 2, 3, 4, 5, ..., 14
 * 
 * @param total 
 * @param active 
 * @param maxItems 
 */
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
            getMiddleValue(
                preferredNumPagesBoundary,
                leftUpperBoundary,
                leftLowerBoundary
            ),
        rightBoundary =
            active +
            getMiddleValue(
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
