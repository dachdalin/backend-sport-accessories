/** Expands `#rgb`/`#rrggbb` to a normalized lowercase `#rrggbb`, or null if not a valid hex color. */
export function expandHex(value: string): string | null {
    const match = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.exec(value.trim());

    if (!match) {
        return null;
    }

    const hex = match[1];
    const full =
        hex.length === 3
            ? hex
                  .split('')
                  .map((c) => c + c)
                  .join('')
            : hex;

    return `#${full.toLowerCase()}`;
}

function relativeLuminance(hex: string): number {
    const int = parseInt(hex.slice(1), 16);
    const channels = [(int >> 16) & 255, (int >> 8) & 255, int & 255].map(
        (c) => {
            const s = c / 255;

            return s <= 0.03928 ? s / 12.92 : ((s + 0.055) / 1.055) ** 2.4;
        },
    );

    return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
}

/** WCAG contrast ratio (1–21) between two colors, or null if either isn't a valid hex color. */
export function contrastRatio(colorA: string, colorB: string): number | null {
    const hexA = expandHex(colorA);
    const hexB = expandHex(colorB);

    if (!hexA || !hexB) {
        return null;
    }

    const lumA = relativeLuminance(hexA);
    const lumB = relativeLuminance(hexB);
    const [lighter, darker] = lumA > lumB ? [lumA, lumB] : [lumB, lumA];

    return (lighter + 0.05) / (darker + 0.05);
}
