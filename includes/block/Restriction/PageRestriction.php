<?php
/**
 * A Block restriction object of type 'Page'.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

namespace MediaWiki\Block\Restriction;

class PageRestriction extends AbstractRestriction {

	/**
	 * {@inheritdoc}
	 */
	const TYPE = 'page';

	/**
	 * {@inheritdoc}
	 */
	const TYPE_ID = 1;

	/**
	 * @var \Title|bool
	 */
	protected $title;

	/**
	 * {@inheritdoc}
	 */
	public function matches( \Title $title ) {
		if ( !$this->getTitle() ) {
			return false;
		}

		return $title->equals( $this->getTitle() );
	}

	/**
	 * Set the title.
	 *
	 * @since 1.33
	 * @param \Title $title
	 * @return self
	 */
	public function setTitle( \Title $title ) {
		$this->title = $title;

		return $this;
	}

	/**
	 * Get Title.
	 *
	 * @since 1.33
	 * @return \Title|null
	 */
	public function getTitle() {
		if ( $this->title === null ) {
			$this->title = \Title::newFromID( $this->value );

			// If the title does not exist, set to false to prevent multiple database
			// queries.
			if ( $this->title === null ) {
				$this->title = false;
			}
		}

		return $this->title ?? null;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function newFromRow( \stdClass $row ) {
		$restriction = parent::newFromRow( $row );

		// If the page_namespace and the page_title were provided, add the title to
		// the restriction.
		if ( isset( $row->page_namespace ) && isset( $row->page_title ) ) {
			// Clone the row so it is not mutated.
			$row = clone $row;
			$row->page_id = $row->ir_value;
			$title = \Title::newFromRow( $row );
			$restriction->setTitle( $title );
		}

		return $restriction;
	}
}
