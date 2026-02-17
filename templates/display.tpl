<!DOCTYPE html>
<html lang="{$currentLocale|replace:"_":"-"}">

<head>
	<meta charset="{$defaultCharset|escape}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>
	{if $isTitleHtml}
		{translate key="article.pageTitle" title=$title|strip_tags|escape}
	{else}
		{translate key="article.pageTitle" title=$title|escape}
	{/if}
	</title>

	{load_header context="frontend"}
</head>

<body class="gwj-body">

	<header class="gwj-header">

		<div class="gwj-title">
			{if $isTitleHtml}
				{$title|strip_unsafe_html}
			{else}
				{$title|escape}
			{/if}
		</div>

		<div class="gwj-actions">
			<a href="{$parentUrl}" class="gwj-btn gwj-btn-secondary">
				← {if $issue}{translate key="issue.return"}{else}{translate key="article.return"}{/if}
			</a>

			<a href="{$pdfUrl}" class="gwj-btn" download>
				⬇ {translate key="common.download"}
			</a>
		</div>

	</header>

	{if !$isLatestPublication}
		<div class="gwj-notice">
			{$datePublished}
		</div>
	{/if}

	<div class="gwj-pdf-container">
		<iframe id="gwjPdfFrame" title="{$galleyTitle}" allow="fullscreen"></iframe>
	</div>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var urlBase = "{$pluginUrl}/pdfjs/web/viewer.html?file=";
			var pdfUrl = {$pdfUrl|json_encode:JSON_UNESCAPED_SLASHES};
			document.getElementById("gwjPdfFrame").src = urlBase + encodeURIComponent(pdfUrl);
		});
	</script>

</body>
</html>
