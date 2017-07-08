<?php
/*
 * This file is part of the pdf2xml package.
 * https://github.com/sbignet/pdf2xml
 * (c) Stéphane Bignet <github@bigstef.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if(isset($argv[0]))
{
	foreach($argv as $arg)
	{
		if($arg === '-h' || $arg === '--help')
		{
			pdf2xml_help();
		}
	}
}

//Command option
$shortopts  = '';
$shortopts .= 'i:';  // input file
$shortopts .= 'o::'; // output file

//json     => output log in json format
//validate => to validate a xml file
$longopts = array('json', 'validate');

$options = getopt($shortopts, $longopts);

if(!empty($options))
{
	include 'pdf2xml_lib.php';
	//Check input file
	if(isset($options['i']) && is_file($options['i']))
	{
		$input     = new SplFileInfo($options['i']);
		$input_ext = $input->getExtension();
		if(isset($options['validate']) && $input_ext === 'xml')
		{
			$errors = pdf2xml_validating($input->getRealPath());
			if(isset($options['json']))
			{
				$json = array('errors' => array());
				echo json_encode(pdf2xml_validating_output_json($errors, $json),  JSON_PRETTY_PRINT);
			}
			else
			{
				if(isset($errors[0]))
				{
					echo pdf2xml_validating_output_str($errors);
				}
				else
				{
					echo PDF_XML_ERROR_GOOD.'Well formated file!'.PDF_XML_ERROR_END.PHP_EOL;
				}
			}
			exit;
		}
		else
		{
			//Check output file
			$output    = false;
			switch($input->getExtension())
			{
				case 'pdf':
					if(isset($options['o']))
					{
						$output = new SplFileInfo($options['o']);
					}
					else
					{
						$output = new SplFileInfo($input->getPathInfo().'/'.$input->getBasename('.pdf').'.xml');
					}
				break;
				case 'xml':
					if(isset($options['o']))
					{
						$output = new SplFileInfo($options['o']);
					}
					else
					{
						$output = new SplFileInfo($input->getPathInfo().'/'.$input->getBasename('.xml').'.pdf');
					}
				break;
				default:
					$msg = 'input => "'.$input->getRealPath().'" is not a pdf file or a xml file!';
					echo (isset($options['json']))? json_encode(array('errors' => array($msg))).PHP_EOL: $msg.PHP_EOL;                
					exit;
			}
			if($output !== false)
			{
				$output_ext = $output->getExtension();
				if(!is_file($output->getPathname()))
				{
					if(!is_dir($output->getPath()))
					{
						mkdir($output->getPath(), 0777, true);
					}
					if(touch($output->getPathname()))
					{
						file_put_contents($output->getPathname(), '');
						$output = new SplFileInfo($output->getPathname());
					}
					else
					{
						$msg = 'The  output file can\'t be created!';
						echo (isset($options['json']))? json_encode(array('errors' => array($msg))).PHP_EOL: $msg.PHP_EOL;                
						exit;
					}
				}
				switch($input_ext.'_'.$output_ext)
				{
					case 'pdf_xml':
						pdf2xml($input->getRealPath(), $output->getRealPath());
					break;
					case 'xml_pdf':
						$errors = pdf2xml_validating($input->getRealPath());
						if(isset($errors[0]))
						{
							if(isset($options['json']))
							{
								$json = array('errors' => array());
								echo json_encode(pdf2xml_validating_output_json($errors, $json),  JSON_PRETTY_PRINT);
							}
							else
							{
								echo pdf2xml_validating_output_str($errors);
							}
						}
						else
						{
							xml2pdf($input->getRealPath(), $output->getRealPath());
						}
					break;
					case 'xml_xml':
					case 'pdf_pdf':
						$msg = 'input and output file can\'t have the same extension! ('.$input_ext.' / '.$output_ext.')';
						echo (isset($options['json']))? json_encode(array('errors' => array($msg))).PHP_EOL: $msg.PHP_EOL;                
						exit;
					break;
					default:
					{
						$msg = 'output => "'.$output->getRealPath().'" is not a pdf file or a xml file!';
						echo (isset($options['json']))? json_encode(array('errors' => array($msg))).PHP_EOL: $msg.PHP_EOL;                
						exit;
					}
				}
			}
		}
	}
	else
	{
		$msg = 'input => "'.$options['i'].'" is not a file!';
		echo (isset($options['json']))? json_encode(array('errors' => array($msg))).PHP_EOL: $msg.PHP_EOL;
		exit;
	}
}
else
{
	pdf2xml_help();
}

//Help txt
function pdf2xml_help()
{
	echo '  pdf2xml options command are:'.PHP_EOL;
	echo '      -i         => input file  (pdf or xml) (REQUIRED)'.PHP_EOL;
	echo '      -o         => output file (xml or pdf) (optionnal)'.PHP_EOL;
	echo '      --json     => output log in json format'.PHP_EOL;
	echo '      --validate => to validate a xml file before to converse it in pdf file'.PHP_EOL;
	echo '      -h, --help => print this help'.PHP_EOL;
	exit;
}