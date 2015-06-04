<?php

/**
 * Searchable - interface to implement in order to be a searchable DO
 *
 * Originally created by Gabriele Brosulo <gabriele.brosulo@zirak.it>
 *
 * @author Firebrand <developers@firebrand.nz>
 * @creation-date 04-June-2015
 */
interface Searchable {
	
	/**
	 * Filter array
	 * eg. array('Disabled' => 0);
	 * @return string
	 */
	public static function getSearchFilter();
	
	/**
	 * Fields that compose the Title
	 * eg. array('Title', 'Subtitle');
	 * @return array
	 */
	public function getTitleFields();
	
	/**
	 * Fields that compose the Content
	 * eg. array('Teaser', 'Content');
	 * @return array
	 */
	public function getContentFields();
	
	/**
	 * Parent objects that should be displayed in search results.
	 * @return SiteTree or SearchableLinkable
	 */
	public function getOwner();
	
	/**
	 * Whatever this specific Searchable should be included in search results.
	 * This allows you to exclude some DataObjects from search results.
	 * It plays more or less the same role that ShowInSearch plays for SiteTree.
	 * @return boolean
	 */
	public function IncludeInSearch();
}
