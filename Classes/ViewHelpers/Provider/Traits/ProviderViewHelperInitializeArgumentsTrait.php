<?php


namespace Pixelant\PxaDataProvider\ViewHelpers\Provider\Traits;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * Trait introduces the standard initializeArguments() function for provider view helpers with "data" and "object"
 * @package ViewHelpers\Provider\Traits
 */
trait ProviderViewHelperInitializeArgumentsTrait
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument(
            'data',
            'array',
            'Data to provide',
            false,
            []
        );
        $this->registerArgument(
            'object',
            'object',
            'An object to provide data from',
            false
        );
    }
}
