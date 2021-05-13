<?php

declare(strict_types=1);

namespace Roave\InfectionStaticAnalysis\Psalm;

use Infection\Mutant\Mutant;
use Psalm\Internal\Analyzer\ProjectAnalyzer;

use function array_key_exists;
use function count;

/**
 * @internal
 *
 * @final not explicitly final because we don't yet have a uniform API for this type of analysis
 */
class RunStaticAnalysisAgainstMutant
{
    private ProjectAnalyzer $projectAnalyzer;

    public function __construct(ProjectAnalyzer $projectAnalyzer)
    {
        $this->projectAnalyzer = $projectAnalyzer;
    }

    public function isMutantStillValidAccordingToStaticAnalysis(Mutant $mutant): bool
    {
        $path     = $mutant->getFilePath();
        $paths    = [$mutant->getFilePath()];
        $codebase = $this->projectAnalyzer->getCodebase();

        $codebase->reloadFiles($this->projectAnalyzer, $paths);
        $codebase->analyzer->analyzeFiles($this->projectAnalyzer, count($paths), false);

        return ! array_key_exists(
            $path,
            $codebase->file_reference_provider->getExistingIssues()
        );
    }
}
