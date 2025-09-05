<?php

$file = __DIR__.'/vendor/easyrdf/easyrdf/lib/Parser/RdfXml.php';

$patchedContent = str_replace(
    <<<'PHP'
        /** @ignore */
        protected function initXMLParser()
        {
            if (!isset($this->xmlParser)) {
                $parser = xml_parser_create_ns('UTF-8', '');
                xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
                xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                xml_set_element_handler($parser, 'startElementHandler', 'endElementHandler');
                xml_set_character_data_handler($parser, 'cdataHandler');
                xml_set_start_namespace_decl_handler($parser, 'newNamespaceHandler');
                xml_set_object($parser, $this);
                $this->xmlParser = $parser;
            }
        }
    PHP,
    <<<'PHP'
        /** @ignore */
        protected function initXMLParser()
        {
            if (!isset($this->xmlParser)) {
                $parser = xml_parser_create_ns('UTF-8', '');
                xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
                xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
                xml_set_element_handler($parser, $this->startElementHandler(...), $this->endElementHandler(...));
                xml_set_character_data_handler($parser, $this->cdataHandler(...));
                xml_set_start_namespace_decl_handler($parser, $this->newNamespaceHandler(...));
                $this->xmlParser = $parser;
            }
        }
    PHP,
    file_get_contents($file),
);

file_put_contents($file, $patchedContent);
