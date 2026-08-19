---
paths:
  - 'app/Models/*.php'
---

# Models

## Uncountable-noun models need explicit $table
Laravel's pluralizer treats "media" (and other uncountable nouns) as already plural, so a model named e.g. `SocialMedia` auto-resolves to table `social_media`, not `social_medias`. If the migration/table was created as `social_medias` (matching the plural route/resource name used elsewhere in this app), the model must set `protected $table = 'social_medias';` explicitly or every query 500s with "no such table". Check this whenever a model name ends in an irregular/uncountable English noun (media, data, series, species, ...).
