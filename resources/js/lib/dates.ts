/** Formats a `Date` as a local (not UTC) `YYYY-MM-DD` string. */
export function toISODate(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

/** Parses a `YYYY-MM-DD` string as a local `Date`, or null if it isn't one. */
export function parseISODate(value: string): Date | null {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value.trim());

    if (!match) {
        return null;
    }

    const year = Number(match[1]);
    const month = Number(match[2]);
    const day = Number(match[3]);
    const date = new Date(year, month - 1, day);

    return Number.isNaN(date.getTime()) ? null : date;
}

export function todayISO(): string {
    return toISODate(new Date());
}

/** Formats a `YYYY-MM-DD` string for display, e.g. "Aug 30, 2026". Null if invalid/empty. */
export function formatDisplayDate(value: string): string | null {
    const date = parseISODate(value);

    return date
        ? date.toLocaleDateString(undefined, {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
          })
        : null;
}
