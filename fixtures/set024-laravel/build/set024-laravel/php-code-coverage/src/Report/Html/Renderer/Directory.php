<?php

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Html;

use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\AbstractNode as Node;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
/**
 * Renders a directory node.
 */
final class Directory extends \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Html\Renderer
{
    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function render(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\Directory $node, string $file) : void
    {
        $template = new \_PhpScoper5b2c11ee6df50\Text_Template($this->templatePath . 'directory.html', '{{', '}}');
        $this->setCommonTemplateVariables($template, $node);
        $items = $this->renderItem($node, \true);
        foreach ($node->getDirectories() as $item) {
            $items .= $this->renderItem($item);
        }
        foreach ($node->getFiles() as $item) {
            $items .= $this->renderItem($item);
        }
        $template->setVar(['id' => $node->getId(), 'items' => $items]);
        $template->renderTo($file);
    }
    protected function renderItem(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\AbstractNode $node, bool $total = \false) : string
    {
        $data = ['numClasses' => $node->getNumClassesAndTraits(), 'numTestedClasses' => $node->getNumTestedClassesAndTraits(), 'numMethods' => $node->getNumFunctionsAndMethods(), 'numTestedMethods' => $node->getNumTestedFunctionsAndMethods(), 'linesExecutedPercent' => $node->getLineExecutedPercent(\false), 'linesExecutedPercentAsString' => $node->getLineExecutedPercent(), 'numExecutedLines' => $node->getNumExecutedLines(), 'numExecutableLines' => $node->getNumExecutableLines(), 'testedMethodsPercent' => $node->getTestedFunctionsAndMethodsPercent(\false), 'testedMethodsPercentAsString' => $node->getTestedFunctionsAndMethodsPercent(), 'testedClassesPercent' => $node->getTestedClassesAndTraitsPercent(\false), 'testedClassesPercentAsString' => $node->getTestedClassesAndTraitsPercent()];
        if ($total) {
            $data['name'] = 'Total';
        } else {
            if ($node instanceof \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\Directory) {
                $data['name'] = \sprintf('<a href="%s/index.html">%s</a>', $node->getName(), $node->getName());
                $data['icon'] = '<span class="glyphicon glyphicon-folder-open"></span> ';
            } else {
                $data['name'] = \sprintf('<a href="%s.html">%s</a>', $node->getName(), $node->getName());
                $data['icon'] = '<span class="glyphicon glyphicon-file"></span> ';
            }
        }
        return $this->renderItemTemplate(new \_PhpScoper5b2c11ee6df50\Text_Template($this->templatePath . 'directory_item.html', '{{', '}}'), $data);
    }
}
