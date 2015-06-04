# Firebrand Searchable DataObjects

Firebrand Searchable DataObjects is a fork of [Zirak's Searchable DataObjects](https://github.com/g4b0/silverstripe-searchable-dataobjects).

Firebrand's version will return Pages matching a search criteria or having related Data Objects matching the search criteria. Zirak's version returns the DataObjects individual data objects.

## Introduction

Complexe SilverStripe pages will sometimes need to be divided up in various parts using DataObjects. Out of the box SilverStripe will only index the content in the main WYSIWYG area of a page. This means that related DataObjects will not be indexed and that their parent pages will not be returned in search results.

## Requirements

 * SilverStripe 3.1
 * zirak/htmlpurifier

### Installation

Install the module through [composer](http://getcomposer.org):

	composer require firebrandhq/searchable-dataobjects
  composer update

Make the DataObject (or Pages) implement `Searchable` or `SearchableLinkable` interface. You need to define `getSearchFilter()`, `getTitleFields()`, `getContentFields()`, `getOwner()`, `IncludeInSearch()`). Classes that implement the `SearchableLinkable` interface, must additionnaly define a `Link()` function.

DataObjects that are accessible via a URL should implement `SearchableLinkable` while DataObjects that belong to a parent object without being reable directly should implement `Searchable`.

```php
class DoNews extends DataObject implements SearchableLinkable {

	private static $db = array(
		'Title' => 'Varchar',
		'Subtitle' => 'Varchar',
		'News' => 'HTMLText',
		'Date' => 'Date',
	);
	private static $has_one = array(
		'Page' => 'PghNews'
	);
	
	private static $has_many = array(
		'Asides' => 'Aside'
	);

	/**
	 * Filter array
	 * eg. array('Disabled' => 0);
	 * @return array
	 */
	public static function getSearchFilter() {
		return array();
	}

	/**
	 * Fields that compose the Title
	 * eg. array('Title', 'Subtitle');
	 * @return array
	 */
	public function getTitleFields() {
		return array('Title');
	}

	/**
	 * Fields that compose the Content
	 * eg. array('Teaser', 'Content');
	 * @return array
	 */
	public function getContentFields() {
		return array('Subtitle', 'Content');
	}
	
	/**
	 * Parent objects that should be displayed in search results.
	 * @return SiteTree or SearchableLinkable
	 */
	public function getOwner() {
		return $this;
	}
	
	/**
	 * Whatever this specific Searchable should be included in search results.
	 * This allows you to exclude some DataObjects from search results.
	 * It plays more or less the same role that ShowInSearch plays for SiteTree.
	 * @return boolean
	 */
	public function IncludeInSearch() {
		return true;
	}
	
	/**
	 * Link to access this DO
	 * @return string
	 */
	public function Link() {
		$this->Page->Link('news/' . $this->ID);
	}
}
```

```php
class Aside extends DataObject implements Searchable {

	private static $db = array(
		'Header' => 'Varchar',
		'Content' => 'HTMLText',
	);
	private static $has_one = array(
		'DoNews' => 'DoNews'
	);


	/**
	 * Filter array
	 * eg. array('Disabled' => 0);
	 * @return array
	 */
	public static function getSearchFilter() {
		return array();
	}

	/**
	 * Fields that compose the Title
	 * eg. array('Title', 'Subtitle');
	 * @return array
	 */
	public function getTitleFields() {
		return array('Header');
	}

	/**
	 * Fields that compose the Content
	 * eg. array('Teaser', 'Content');
	 * @return array
	 */
	public function getContentFields() {
		return array('Content');
	}
	
	/**
	 * Parent objects that should be displayed in search results.
	 * @return SiteTree or SearchableLinkable
	 */
	public function getOwner() {
		return $this->DoNews;
	}
	
	/**
	 * Whatever this specific Searchable should be included in search results.
	 * This allows you to exclude some DataObjects from search results.
	 * It plays more or less the same role that ShowInSearch plays for SiteTree.
	 * @return boolean
	 */
	public function IncludeInSearch() {
		return true;
	}
}
```


Extend Page and the desired DataObjects through the following yaml:

```YAML
Page:
	extensions:
		- SearchableDataObject
DoNews:
	extensions:
		- SearchableDataObject
```

Run a `dev/build` and then populate the search table running PopulateSearch task:

	sake dev/build "flush=all"
	sake dev/tasks/PopulateSearch

When you save your pages or you DataObject, they will automatically update their entry in the search table.

### Note

Searchable DataObjects module use Mysql NATURAL LANGUAGE MODE search method, so during your tests be sure not to have all DataObjetcs
with the same content, since words that are present in 50% or more of the rows are considered common and do not match.

From MySQL manual entry [http://dev.mysql.com/doc/refman/5.1/en/fulltext-search.html]:

A natural language search interprets the search string as a phrase in natural human language (a phrase in free text). There are no special operators.
The stopword list applies. In addition, words that are present in 50% or more of the rows are considered common and do not match. 
Full-text searches are natural language searches if the IN NATURAL LANGUAGE MODE modifier is given or if no modifier is given.
