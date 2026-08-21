const UNITS: Array<[Intl.RelativeTimeFormatUnit, number]> = [
    ['year', 60 * 60 * 24 * 365],
    ['month', 60 * 60 * 24 * 30],
    ['week', 60 * 60 * 24 * 7],
    ['day', 60 * 60 * 24],
    ['hour', 60 * 60],
    ['minute', 60],
];

/**
 * Format a timestamp as a short relative string (e.g. "2h ago", "just now").
 */
export function timeAgo(timestamp: string, locale: string = 'en'): string {
    const seconds = Math.round(
        (Date.now() - new Date(timestamp).getTime()) / 1000,
    );

    if (seconds < 60) {
        return locale === 'km' ? 'ឥឡូវនេះ' : 'just now';
    }

    const formatter = new Intl.RelativeTimeFormat(locale, { style: 'short' });

    for (const [unit, secondsInUnit] of UNITS) {
        const value = Math.floor(seconds / secondsInUnit);

        if (value >= 1) {
            return formatter.format(-value, unit);
        }
    }

    return formatter.format(-seconds, 'second');
}
