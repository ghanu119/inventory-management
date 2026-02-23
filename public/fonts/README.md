# PDF fonts (Gujarati / Hindi)

For invoice PDFs to render **Gujarati** (and Devanagari/Hindi) text, place the following font file here:

- **NotoSansGujarati-Regular.ttf**

Then run (optional, installs the font automatically):

```bash
php artisan pdf:install-gujarati-font
```

Or download manually from [Google Fonts – Noto Sans Gujarati](https://fonts.google.com/noto/specimen/Noto+Sans+Gujarati) (download family, extract the TTF) and save as `NotoSansGujarati-Regular.ttf` in this folder.
