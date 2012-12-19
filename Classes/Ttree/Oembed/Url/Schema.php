<?php
namespace Ttree\Oembed\Url;

/*                                                                        *
 * This script belongs to the Flow package "Ttree.Oembed".                *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * oEmbed Photo
 *
 * @package Ttree.Oembed
 * @author  Dominique Feyer <dfeyer@ttree.ch>
 */
class Schema {

	const WILDCARD_CHARACTER = '*';

	/**
	 * Scheme
	 *
	 * @var string
	 */
	protected $scheme = '';

	/**
	 * Regular Expression Pattern.
	 *
	 * @var string
	 */
	protected $pattern = '';

	public function __construct($scheme) {
		if (!is_string($scheme)) {
			throw new InvalidArgumentException(
				'$scheme must be a string.'
			);
		}

		$this->scheme = $scheme;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->scheme;
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public function match($url) {
		if (!$this->pattern) {
			$this->pattern = self::buildPatternFromScheme($this);
		}

		return ( bool )preg_match($this->pattern, $url);
	}

	/**
	 * @param Schema $scheme
	 * @return string
	 */
	static protected function buildPatternFromScheme(self $scheme) {
		// Generate a unique random string.
		$uniq = md5(mt_rand());

		// Replace the wildcard sub-domain if exists.
		$scheme = str_replace(
			'://' . self::WILDCARD_CHARACTER . '.',
			'://' . $uniq,
			$scheme->getScheme()
		);

		// Replace the wildcards.
		$scheme = str_replace(
			self::WILDCARD_CHARACTER,
			$uniq,
			$scheme
		);

		// Set the pattern wrap.
		$wrap = '|';

		// Quote the pattern,
		$pattern = preg_quote($scheme, $wrap);

		// Replace the unique string by the character class.
		$pattern = str_replace($uniq, '.*', $pattern);

		// Return the wrapped pattern.
		return $wrap . $pattern . $wrap . 'iu';
	}

	public function getScheme() {
		return $this->scheme;
	}
}

?>