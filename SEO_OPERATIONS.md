# SEO production and monitoring runbook

The application generates crawl directives, self-referencing canonicals, structured data and an XML image sitemap. Search performance still depends on correct production configuration and ongoing real-world measurement.

## Before launch

1. Set `APP_URL` to the single preferred HTTPS origin, including the chosen `www` or non-`www` hostname. Redirect every alternative hostname and HTTP URL to it.
2. Set the real legal business name, support email, phone and complete postal address in the production environment. Do not publish a placeholder address or phone number in business markup.
3. Add the Search Console verification token as `GOOGLE_SITE_VERIFICATION`. Add `BING_SITE_VERIFICATION` if Bing Webmaster Tools will be used.
4. Confirm that `https://your-domain/sitemap.xml` and `https://your-domain/robots.txt` return HTTP 200, then submit the sitemap in Google Search Console and Bing Webmaster Tools.
5. Validate the homepage, one category, one product and one article in Google's Rich Results Test and Schema Markup Validator.
6. Create Google Merchant Center product data after prices, stock, shipping, returns, GTIN/MPN information and supplier details are final. Structured data and the feed must agree.
7. Create or claim a Google Business Profile only if customers can genuinely visit the stated location or the business qualifies as a service-area business. Keep the name, address, phone and hours consistent with the website.

## Weekly checks

- Search Console: indexing changes, manual actions, security issues, sitemap processing and product enhancement errors.
- Merchant Center: disapprovals, price/availability mismatches and shipping or return-policy warnings.
- Server monitoring: 5xx rate, response time, crawl spikes and broken image requests.
- Orders/support: questions that should become clearer product copy or a new journal guide.

## Monthly checks

- Search Console Performance: non-brand landing pages, click-through rate, device split and pages losing impressions.
- Core Web Vitals field report: LCP, INP and CLS at the 75th percentile on mobile and desktop. Investigate by page template, not only site-wide averages.
- Crawl a staging copy with a crawler to find broken internal links, redirect chains, duplicate titles, missing descriptions and accidental `noindex` directives.
- Review one-quarter of product descriptions and all changed policy or editorial content for accuracy and freshness.

## Off-site authority work

Backlinks cannot be implemented in source code. Earn relevant references through verified producer profiles, Uttarakhand/Himachal business directories, genuine press coverage, useful original guides, local partnerships and supplier pages. Do not buy links or exchange large numbers of keyword-rich links.

## Release regression commands

```text
php artisan test
vendor\bin\phpstan.bat analyse --memory-limit=1G
npm.cmd run build
php artisan images:optimize-catalogue
```
