# pdf2xml
A php command for convert pdf file structure in a xml file (and inversely)

pdf2xml options command are:
      -i         => input file  (pdf or xml) (REQUIRED)
      -o         => output file (xml or pdf) (optionnal)
      --json     => output log in json format
      --validate => to validate a xml file before to converse it in pdf file
      -h, --help => print this help

Exemple:

	To converse a pdf in a xml file
		./pdf2xml -i=/path/to/file.pdf
		=> the file output is /path/to/file.xml

		You can precise a file for the xml output:
		./pdf2xml -i=/path/to/file.pdf -o=/path/to/another_file.xml

	the reverse way:
		./pdf2xml -i=/path/to/file.xml
		=> the file output is /path/to/file.pdf

		You can precise a file for the pdf output:
		./pdf2xml -i=/path/to/file.xml -o=/path/to/another_file.pdf

	To converse a xml file in a pdf file, you must validate it with this command
		for a shell output:
			./pdf2xml -i=/path/to/file.xml --validate

		for a json output
			./pdf2xml -i=/path/to/file.xml --validate --json

	You can include this lib:
		include 'phar://pdf2xml.phar/pdf2xml_lib.php';
		