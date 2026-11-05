# Builder JSON round-trip fix

Support ticket 88903 reports an empty Pile builder after editing text and saving. PixTypes 2.0.2 unslashes submitted field values before calling the WordPress metadata API, which unslashes them again. Quotes, backslashes and Unicode JSON escape sequences can therefore be corrupted. The revision copy and restore paths also pass unslashed encoded JSON to the metadata API.

This patch re-slashes the encoded builder value at those three metadata boundaries. Other field normalization, permission and nonce checks retain their behavior. It preserves future writes; it cannot recover already corrupted builder data. Recover from a known-good database backup/revision only after testing on a copy.

Verified on a fresh WordPress Studio installation with actual WordPress metadata storage: original source fails six save/revision/restore assertions; corrected source passes quoted HTML/backslashes and Unicode JSON across repeated save, revision copy and restore. Invalid nonce leaves data intact. Test: `wp eval-file tests/builder-roundtrip.php` with PixTypes active. No customer site accessed or modified.
