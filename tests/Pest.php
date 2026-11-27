<?php

use BladeUi\Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

/** Extract class tokens from the first element (or the first element of the given tag) in rendered HTML. */
function bladeUiClassTokens(string $html, ?string $tag = null): array
{
    $pattern = $tag === null
        ? '/class="([^"]*)"/'
        : '/<'.preg_quote($tag, '/').'(?=\s)(?:[^>"\']|"[^"]*"|\'[^\']*\')*?\sclass="([^"]*)"/s';

    preg_match($pattern, $html, $match);

    return preg_split('/\s+/', trim($match[1] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
}
