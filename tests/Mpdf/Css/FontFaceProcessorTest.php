<?php


namespace Mpdf\Css;


use Mockery;
use Mpdf\Mpdf;

class FontFaceProcessorTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var \Mpdf\Css\FontFaceProcessor
	 */
	protected $fontFaceProcessor;

	/**
	 * @var \Psr\Log\Test\TestLogger
	 */
	protected $logger;

	/**
	 * @var \Mpdf\Mpdf|\Mockery\MockInterface
	 */
	protected $mpdf;

	/**
	 * @inheritDoc
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->mpdf = Mockery::mock(Mpdf::class);
		$this->mpdf->shouldIgnoreMissing();
		//$this->logger = new TestLogger();
		$this->fontFaceProcessor = new FontFaceProcessor($this->mpdf);
	}

	public function testFontFacesProcessed()
	{
		$this->mpdf->shouldReceive('AddFontDirectory')
			->with('/path/to/font')
			->once();

		$cssString = "@font-face {" .
			"font-family: 'Test Font 1';" .
			"src: url('/path/to/font/file1.ttf') format('truetype');" .
			"}\n" .
			"@font-face {\n" .
			"\tfont-family:\t'Test Font 2';\n" .
			"\tsrc:\turl('/path/to/font/file2.ttf')\n\t  format('truetype');\n" .
			"}\n" .
			"#tag-id {\n}\n/* comment */\n.tag-class {\n}";

		$result = $this->fontFaceProcessor->ProcessCSS($cssString);

		$this->assertNotContains('@font-face', $result);

		$this->assertArrayHasKey('test_font_1', $this->mpdf->fontdata);

		$this->assertArrayHasKey('test_font_2', $this->mpdf->fontdata);
	}

}