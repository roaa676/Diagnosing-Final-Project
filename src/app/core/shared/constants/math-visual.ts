export function getMathRepresentation(question: string): string[] {

    if (!question) {
        return [];
    }

    const numbers =
        question.match(/\d+/g)?.map(Number) || [];

    if (
        question.includes('+') &&
        numbers.length >= 2
    ) {

        return [
            '🍎'.repeat(numbers[0]),
            '➕',
            '🍎'.repeat(numbers[1])
        ];
    }

    if (
        question.includes('-') &&
        numbers.length >= 2
    ) {

        return [
            '🍪'.repeat(numbers[0]),
            '➖',
            '🍪'.repeat(numbers[1])
        ];
    }

    if (
        question.includes('أصابع')
    ) {

        return ['✋'];
    }

    if (
        question.includes('يمثل العدد')
    ) {

        const number =
            numbers[0] || 5;

        return [
            '⭐'.repeat(number)
        ];
    }

    if (
        question.includes('أكبر')
    ) {

        return [
            '🐘',
            '🆚',
            '🐭'
        ];
    }

    if (
        question.includes('أصغر')
    ) {

        return [
            '🐭',
            '🆚',
            '🐘'
        ];
    }

    if (
        question.includes('النمط') ||
        question.includes('التالي')
    ) {

        return [
            '🚂 🚃 🚃 🚃'
        ];
    }

    return ['🧮'];
}