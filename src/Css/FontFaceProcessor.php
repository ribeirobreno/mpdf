<?php


namespace Mpdf\Css;


class FontFaceProcessor
{

	/**
	 * @var \Mpdf\Mpdf
	 */
	private $mpdf = null;

	/**
	 * @var string
	 */
	private $css = '';

	/**
	 * @var string[]
	 */
	private static $directories = [];

	/**
	 * FontFace constructor.
	 * @param \Mpdf\Mpdf $mpdf
	 */
	public function __construct(\Mpdf\Mpdf $mpdf)
	{
		$this->mpdf = $mpdf;
	}


	/**
	 * @param string $css
	 * @return string
	 */
	public function ProcessCSS($css)
	{
		if (self::HasFontFace($css)) {
			$this->css = $css;
			$css = null;

			// Extract rules from CSS string.
			$rules = $this->ExtractRules();
			// Try to extract font data from @font-face rules.
			$fonts = $this->ParseRuleset($rules);

			// Set font information at the \Mpdf\Mpdf instance.
			$this->UpdateAvailableFonts($fonts);
		}

		return $this->css;
	}

	/**
	 * @param string $css
	 * @return bool
	 */
	public static function HasFontFace($css)
	{
		return strpos($css, '@font-face') !== false;
	}

	/**
	 * @return array
	 */
	private function ExtractRules()
	{
		$pattern = '/@font-face\s*\{(.*?)\}/sim';

		$matches = [];
		preg_match_all($pattern, $this->css, $matches);

		$this->css = preg_replace($pattern, '', $this->css);

		return isset($matches[1]) ? $matches[1] : [];
	}

	private function ParseRuleset(array $rules)
	{
		return [];
	}

	private function UpdateAvailableFonts(array $fonts)
	{
		foreach ($fonts as $font) {
			$this->AddFontDirectory($font['path']);
			$this->mpdf->fontdata[$font['name']] = $font['data'];
		}
	}

	/**
	 * @param string $path
	 */
	private function AddFontDirectory($path)
	{
		if (!isset(self::$directories[$path])) {
			$this->mpdf->AddFontDirectory($path);
			self::$directories[$path] = true;
		}
	}
}