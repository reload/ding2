<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * Provides step definitions for interacting with P2.
 */
class P2Context implements Context, SnippetAcceptingContext
{
    /** @var Ding2Context */
    private $ding2Context;

    /**
     * @var array
     *   Save data across scenarios.
     */
    private $dataRegistry = array();

    /** @var array */
    private $lastCreatedList;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->ding2Context = $environment->getContext('Ding2Context');
    }

    /**
     * @Then I should see a link to the create list page
     */
    public function iShouldSeeALinkToTheCreateListPage()
    {
        $this->ding2Context->iSeeALinkTo($this->ding2Context->userPath() .
                                         '/createlist');
    }

    /**
     * @Then the list for followed searches exists
     */
    public function theFollowedSearchesListExists()
    {
        $this->theListExists('follow search');
    }

    /**
     * @Given I have searched for :arg1
     */
    public function iHaveSearchedFor($arg1)
    {
        $this->ding2Context->drupalContext->visitPath('/search/ting/' . urlencode($arg1));
    }

    /**
     * @When I add the search to followed searches
     */
    public function iAddTheSearchToFollowedSearches()
    {
        $followed_searches_id = $this->getListId('follow search');
        $this->moreDropdownSelect('/dinglist/attach/search_query/'. $followed_searches_id, "Couldn't find button to add search to list");
    }

    /**
     * Select an item in a "More.." dropdown.
     *
     * @param string $link
     *   The link the item should point to.
     * @param string $errorMessage
     *   Exception message if the link could not be found.
     */
    public function moreDropdownSelect($link, $errorMessage)
    {
        $page = $this->ding2Context->minkContext->getSession()->getPage();
        $button = $page->find('css', '.ding-list-add-button a');
        if (!$button) {
            throw new \Exception("Couldn't find more button");
        }

        try {
            // Mouseover the button to trigger the dropdown. Can't click an
            // invisible link in a real browser.
            $button->mouseOver();
        } catch (UnsupportedDriverActionException $e) {
            // Carry on if the driver doesn't support it.
        }

        // Sadly the links isn't related to the button in any way.
        $link = $page->find('css', 'a[href^="' . $link . '"]');
        if (!$link) {
            throw new \Exception($errorMessage);
        }
        $link->click();
    }

    /**
     * @Then I should see :arg1 on followed searches
     */
    public function iShouldSeeOnFollowedSearches($arg1)
    {
        $followed_searches_id = $this->getListId('follow search');
        $this->ding2Context->drupalContext->visitPath("/list/$followed_searches_id");
        $this->ding2Context->minkContext->assertElementContainsText('.ding-type-ding-list-element .content a', $arg1);
    }

    /**
     * @Given I have followed the search :arg1
     */
    public function iHaveFollowedTheSearch($arg1)
    {
        // Perform search.
        $this->iHaveSearchedFor($arg1);
        $this->iAddTheSearchToFollowedSearches();
        $this->iShouldSeeOnFollowedSearches($arg1);
    }

    /**
     * @When I remove the search :arg1 from followed searches
     */
    public function iRemoveTheSearchFromFollowedSearches($arg1)
    {
        $followed_searches_id = $this->getListId('follow search');
        $this->ding2Context->drupalContext->visitPath('/list/' . $followed_searches_id);
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a:contains("' . $arg1 . '") + form[id^="ding-list-remove-element"] #edit-submit');
        if (!$found) {
            throw new \Exception("Remove link doesn't exist");
        }
        $found->click();
    }

    /**
     * @Then I should not see :arg1 on followed searches
     */
    public function iShouldNotSeeOnFollowedSearches($arg1)
    {
        $followed_searches_id = $this->getListId('follow search');
        $this->ding2Context->drupalContext->visitPath('/list/' . $followed_searches_id);
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a[href^="/search/ting"]:contains("' . $arg1 . '")');
        if ($found && $found->getValue() == $arg1) {
            throw new \Exception("Link to author '$arg1' still exists.");
        }
    }

    /**
     * @When I add the author :author to authors I follow
     */
    public function iAddTheAuthorToAuthorsIFollow($author)
    {
        // Choose book facet.
        $found = $this->ding2Context->minkContext->getSession()->getPage()->find('css', '.form-item-type-bog a');
        if (!$found) {
            throw new \Exception('Book facet not found');
        }
        $found->click();

        $authorLowerCase = strtolower(preg_replace('/\s/', '-', $author));
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.form-item-creator-' . $authorLowerCase . ' a');
        if (!$found) {
            throw new \Exception('Creator facet not found');
        }
        $found->click();

        // Follow link to book.
        $this->ding2Context->minkContext->assertElementContains('.search-result--heading-type', 'Bog');
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.search-result--heading-type:contains("Bog") + h2 > a');
        if (!$found) {
            throw new \Exception("Link to book doesn't exist");
        }
        $found->click();

        // Follow link to follow author.
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a[href^="/dinglist/attach/follow_author/"]');
        if (!$found) {
            throw new \Exception("Link to follow author doesn't exist");
        }
        $found->click();
    }

    /**
     * Get the ID of the named list.
     *
     * @param string $list
     *   List name.
     * @param bool $normalize
     *   Whether to normalize name.
     *
     * @return string
     *   The list id.
     */
    function getListId($list)
    {
        $listName = 'list:' . $list;
        if (!isset($this->dataRegistry[$listName])) {
            // Try to find list by scanning user page.
            $this->ding2Context->minkContext->visitPath($this->ding2Context->userPath());
            $li_elements = $this->ding2Context->minkContext->getSession()->getPage()->findAll('css', 'ul li');
            foreach ($li_elements as $li) {
                $a = $li->find('css', 'a.signature-label');
                if ($a && preg_match('{/list/(\d+)}', $a->getAttribute('href'), $matches)) {
                    $text = trim($a->getText());
                    $this->dataRegistry['list:' . $text] = $matches[1];
                }
            }
        }

        if (!isset($this->dataRegistry[$listName])) {
            throw new \Exception("List id for list $list doesn't exist");
        }
        $listId = $this->dataRegistry[$listName];
        if (!$listId) {
            throw new \Exception("List id for list $list seems to be public");
        }

        return $listId;
    }

    /**
     * @Then I should see :arg1 on the list of followed authors
     */
    public function iShouldSeeOnTheListOfFollowedAuthors($arg1)
    {
        $follow_author_id = $this->getListId('follow author');
        $this->ding2Context->drupalContext->visitPath('/list/' . $follow_author_id);
        $link = '/search/ting/phrase.creator';
        $this->ding2Context->minkContext->assertElementContains('a[href^="' . $link . '"]', $arg1);
    }

    /**
     * @Given I have followed the author :arg1
     */
    public function iHaveFollowedTheAuthor($arg1)
    {
        // First add the author to the list.
        $this->iHaveSearchedFor($arg1);
        $this->iAddTheAuthorToAuthorsIFollow($arg1);

        $this->iShouldSeeOnTheListOfFollowedAuthors($arg1);
    }

    /**
     * @When I remove the author :arg1 from followed authors
     */
    public function iRemoveTheAuthorFromFollowedAuthors($arg1)
    {
        $follow_author_id = $this->getListId('follow author');
        $this->ding2Context->drupalContext->visitPath('/list/' . $follow_author_id);
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a:contains("' . $arg1 . '") + form[id^="ding-list-remove-element"] #edit-submit');
        if (!$found) {
            throw new \Exception("Remove link doesn't exist");
        }
        $found->click();
    }

    /**
     * @Then I should not see :arg1 on followed authors
     */
    public function iShouldNotSeeOnFollowedAuthors($arg1)
    {
        $follow_author_id = $this->getListId('follow author');
        $this->ding2Context->drupalContext->visitPath('/list/' . $follow_author_id);
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a[href^="/search/ting/phrase.creator"]');
        if ($found && $found->getValue() == $arg1) {
            throw new \Exception("Link to author '$arg1' still exists.");
        }
    }

    /**
     * @Given I am on my user consent page
     */
    public function iAmOnMyUserConsentPage()
    {
        if (empty($this->ding2Context->user) || empty($this->ding2Context->user->uid)) {
            throw new \Exception("User doesn't exist");
        }
        $uid = $this->ding2Context->user->uid;
        $this->ding2Context->drupalContext->visitPath("/user/$uid/consent");
    }

    /**
     * @When I check the consent box
     */
    public function iCheckTheConsentBox()
    {
        $checked = $this->ding2Context->minkContext->getSession()->getPage()->find('css', '#edit-loan-history-store');
        if (!$checked) {
            throw new \Exception("Couldn't find consent check box");
        }
        $checked_value = $checked->getValue();
        if ($checked_value) {
            throw new \Exception("Consent checkbox is already checked.");
        }

        $checked->check();
        $this->ding2Context->minkContext->pressButton('edit-submit');
    }

    /**
     * @Then I should see that the consent box is checked
     */
    public function iShouldSeeThatTheConsentBoxIsChecked()
    {
        $this->iAmOnMyUserConsentPage();
        $checked = $this->ding2Context->minkContext->getSession()->getPage()->find('css', '#edit-loan-history-store');
        if (!$checked) {
            throw new \Exception("Couldn't find consent check box");
        }
        $checked_value = $checked->getValue();
        if (!$checked_value) {
            throw new \Exception("Consent checkbox is not checked.");
        }
    }

    /**
     * @When I uncheck the consent box
     */
    public function iUncheckTheConsentBox()
    {
        // First go to consent page, check consent box, and go to consent page.
        $this->iCheckTheConsentBox();
        $this->iAmOnMyUserConsentPage();

        $checked = $this->ding2Context->minkContext->getSession()->getPage()->find('css', '#edit-loan-history-store');
        if (!$checked) {
            throw new \Exception("Couldn't find consent check box");
        }
        $checked_value = $checked->getValue();
        if (!$checked_value) {
            throw new \Exception("Consent checkbox is not checked.");
        }

        $checked->uncheck();
        $this->ding2Context->minkContext->pressButton('edit-submit');
    }

    /**
     * @Then I should see that the consent box is not checked
     */
    public function iShouldSeeThatTheConsentBoxIsNotChecked()
    {
        $this->iAmOnMyUserConsentPage();
        $checked = $this->ding2Context->minkContext->getSession()->getPage()->find('css', '#edit-loan-history-store');
        if (!$checked) {
            throw new \Exception("Couldn't find consent check box");
        }
        $checked_value = $checked->getValue();
        if ($checked_value) {
            throw new \Exception("Consent checkbox is checked");
        }
    }

    /**
     * @Given I am on my create list page
     */
    public function iAmOnMyCreateListPage()
    {
        $this->ding2Context->minkContext->visit($this->ding2Context->userPath() . '/createlist');
    }

    /**
     * @When I create a new list :title with description :description
     */
    public function iCreateANewListWithDescription($title, $description)
    {
        $page = $this->ding2Context->minkContext->getSession()->getPage();

        $form = $page->find('css', '#ding-list-create-list-form');
        if (!$form) {
            throw new Exception('Could not find form to add new list on page');
        }

        $this->lastCreatedList = [
            'title' => $title,
            'description' => $description,
        ];

        $form->fillField('edit-title', $title);
        $form->fillField('edit-notes', $description);
        $form->pressButton('edit-add-list');
    }

    /**
     * @Then I should be on a list page
     */
    public function iShouldBeOnAListPage()
    {
        $currentUrl = $this->ding2Context->minkContext->getSession()->getCurrentUrl();
        $basePath = $this->ding2Context->minkContext->getMinkParameter('base_url');
        rtrim($basePath, '/') . '/';
        if (!preg_match('{^' . $basePath . '/list/\d+$}', $currentUrl)) {
            throw new Exception($currentUrl . 'is not on a list page');
        }
    }

    /**
     * @Given I have created a list :title
     */
    public function iHaveCreatedAList($title)
    {
        $this->iAmOnMyCreateListPage();
        $this->iCreateANewListWithDescription($title, '');
        $currentUrl = $this->ding2Context->minkContext->getSession()->getCurrentUrl();
        $basePath = $this->ding2Context->minkContext->getMinkParameter('base_url');
        $basePath = rtrim($basePath, '/');
        $match = array();
        if (!preg_match('{^' . $basePath . '/list/(\d+)}', $currentUrl, $match)) {
            throw new \Exception($currentUrl . 'is not on a list page');
        }

        // Save list id.
        $this->dataRegistry["list:$title"] = $match[1];
    }

    /**
     * @When I go to the share link
     */
    public function iGoToTheShareLink()
    {
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.share .menu-item');
        if (!$found) {
            throw new \Exception("Couldn't find link to share list");
        }
        $found->click();
    }

    /**
     * @When I make the list :title public
     * @Given I have made the list :title public
     */
    public function iMakeTheListPublic($title)
    {
        $page = $this->ding2Context->minkContext->getSession()->getPage();
        // Click on list link.
        $this->iGoToTheListPage($title);

        // Click share list.
        $this->iGoToTheShareLink();

        $found = $page->find('css', '#ding-list-list-permissions-form #edit-status');
        if (!$found) {
            throw new \Exception("Couldn't find dropdown menu for sharing list");
        }

        $found->selectOption('public');
        $page->waitFor(10000, function ($page) {
            return $page->find('css', '#status-description:contains("Your list is now public")');
        });

        $form = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '#ding-list-list-permissions-form');
        $form->submit();
    }

    /**
     * @Then I should see that the list :title is marked as public
     */
    public function iShouldSeeThatTheListIsMarkedAsPublic($title)
    {
        $this->iGoToTheListPage($title);
        $this->iGoToTheShareLink();

        $found_select = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '#ding-list-list-permissions-form #edit-status');
        if (!$found_select) {
            throw new \Exception("Couldn't find drop down menu for sharing list");
        }
        $checked = $found_select->getValue();
        if (!$checked || $checked != 'public') {
            throw new \Exception("List is not set to public shared");
        }
    }

    /**
     * @When I go to the public lists page
     */
    public function iGoToThePublicListsPage()
    {
        $this->ding2Context->minkContext->visit('/public-lists');
    }

    /**
     * @Then I should see the public list :title
     */
    public function iShouldSeeThePublicList($title)
    {
        $listId = $this->getListId($title);

        try {
            $this->ding2Context->minkContext->assertElementContainsText('a[href^="/list/' . $listId . '"]', $title);
            return;
        } catch (Exception $e) {
            $nrPages = 1;
            // Get number of pages from pager in the bottom.
            $lastPage = $this->ding2Context->minkContext->getSession()->getPage()
                ->find('css', '.pager-last a');
            if (!$lastPage) {
                throw new Exception("Couldn't find pager");
            }
            $lastPageHref = $lastPage->getAttribute('href');
            if ($lastPageHref) {
                $match = array();
                if (preg_match('{/public-lists\?page=(\d+)}', $lastPageHref, $match)) {
                    $nrPages = $match[1];
                }
            }

            // Search for list on all pages.
            for ($i = 0; $i <= $nrPages; $i++) {
                if ($i) {
                    $this->ding2Context->minkContext->visitPath('/public-lists?page=' . $i);
                }

                $found = $this->ding2Context->minkContext->getSession()->getPage()
                    ->find('css', 'a[href^="/list/' . $listId . '"]');
                if ($found) {
                    $this->ding2Context->minkContext->assertElementContainsText('a[href^="/list/' . $listId . '"]', $title);

                    // We return now, cause we have found the element.
                    return;
                }
            }
        }
        // If we get here, the element hasn't been found.
        throw new Exception("List '$title' couldn't be found on public lists");
    }

    /**
     * @Given I have a link to a public list with the title :title
     */
    public function iHaveALinkToAPublicListWithTheTitle($title)
    {
        $this->iHaveCreatedAList($title);
        $this->iMakeTheListPublic($title);

        // Log in as different user.
        $this->ding2Context->iAmLoggedInAsALibraryUser();
    }

    /**
     * @When I follow the list :title
     */
    public function iFollowTheList($title)
    {
        $listId = $this->getListId($title);
        $this->ding2Context->minkContext->visit("/list/$listId");

        $foundButton = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'form[action="/list/' . $listId . '"] #edit-submit'); //input[type="submit"]');
        if (!$foundButton) {
            throw new \Exception("Couldn't find follow list button");
        }

        $foundButton->click();
    }

    /**
     * @Then I should see the list :title on lists I follow
     */
    public function iShouldSeeTheListOnListsIFollow($title)
    {
        $this->ding2Context->minkContext->visit($this->ding2Context->userPath() . '/dinglists');
        $listsList = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.lists-list a');
        if (!$listsList) {
            throw new \Exception("Couldn't find list of lists");
        }
        $listsList->click();

        $listId = $this->getListId($title);
        $this->ding2Context->minkContext->assertElementContainsText('a[href="/list/' . $listId . '"]', $title);
    }

    /**
     * @Given I am following a public list with the title :title
     */
    public function iAmFollowingAPublicListWithTheTitle($title)
    {
        // Make sure to follow a public list.
        $this->iHaveALinkToAPublicListWithTheTitle($title);
        $this->iFollowTheList($title);
        $this->iShouldSeeTheListOnListsIFollow($title);
    }

    /**
     * @When I unfollow the list with the title :title
     */
    public function iUnfollowTheListWithTheTitle($title)
    {
        $listId = $this->getListId($title);

        $this->ding2Context->minkContext->visit($this->ding2Context->userPath() . '/dinglists');
        $found_list = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.ding-user-lists .lists-list a');
        if (!$found_list) {
            throw new \Exception("Couldn't find link to list of followed lists");
        }
        $found_list->click();

        // Find link to followed list.
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.ding-type-ding-list a[href="/list/' . $listId . '"]');
        if (!$found) {
            throw new \Exception("Couldn't find list '$title' on followed lists");
        }
        $this->ding2Context->minkContext
            ->assertElementContainsText('.ding-type-ding-list a[href="/list/' . $listId . '"]', $title);

        $deleteLink = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '#ding-list-remove-element-ding-list-' . $listId . '-form #edit-submit');
        if (!$deleteLink) {
            throw new \Exception("Couldn't find remove from list button");
        }
        $deleteLink->click();
    }

    /**
     * @Then I should not see the list :title on lists I follow
     */
    public function iShouldNotSeeTheListOnListsIFollow($title)
    {
        $listId = $this->getListId($title);
        $this->ding2Context->minkContext->visit($this->ding2Context->userPath() . '/dinglists');
        $found_list = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.ding-user-lists .lists-list a');
        if (!$found_list) {
            throw new \Exception("Couldn't find link to list of followed lists");
        }
        $found_list->click();

        $this->ding2Context->minkContext
            ->assertElementNotOnPage('.ding-type-ding-list a[href="/list/' . $listId . '"]');
    }

    /**
     * @Given I have created a public list :title
     */
    public function iHaveCreatedAPublicList($title)
    {
        $this->iHaveCreatedAList($title);
        $this->iMakeTheListPublic($title);
        $this->iShouldSeeThatTheListIsMarkedAsPublic($title);
    }

        /**
     * @When I add material :material to the list :title
     */
    public function iAddMaterialToTheList($material, $title)
    {
        $this->ding2Context->minkContext->visitPath('/search/ting/' . urlencode($material));

        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a[href^="/ting/collection"]:contains("' . $material . '")');
        if (!$found) {
            throw new \Exception("Couldn't find search result");
        }
        $found->click();

        // Make sure document is ready before we try to make a mouseover.
        $this->ding2Context->minkContext->getSession()
            ->evaluateScript('window.jQuery(document).ready(function() { return; });');
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.ding-list-add-button a');
        if (!$found) {
            throw new \Exception("Couldn't find more button");
        }
        $this->ding2Context->minkContext->getSession()
            ->evaluateScript('jQuery(document).scrollTo(".ding-list-add-button a");');
        $found->mouseOver();

        // Add material to public list.
        $listLink = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.buttons li a[href^="/dinglist/attach/ting_object"]:contains("' . $title . '")');
        if (!$listLink) {
            throw new \Exception("Couldn't find link to add to list '$title'");
        }
        $listLink->click();
    }

    /**
     * @Given I have added the material :material to the list :list
     */
    public function iHaveAddedTheMaterialToTheList($material, $list)
    {
        $this->iAddMaterialToTheList($material, $list);
        $this->iShouldSeeTheMaterialOnTheList($material, $list);
    }

    /**
     * @Then I should get a confirmation that I added the material to :list list
     */
    public function iShouldGetAConfirmationThatIAddedTheMaterialToList($list)
    {
        $page = $this->ding2Context->minkContext->getSession()->getPage();
        // Wait for popup.
        $page->waitFor(10000, function ($page) {
            return $page->find('css', '.ui-dialog');
        });
        $this->ding2Context->minkContext->assertElementContainsText('.ui-dialog', 'Tilføjet til ' . $list);
    }

    /**
     * @Then I should see the material :material on the list :title
     */
    public function iShouldSeeTheMaterialOnTheList($material, $title)
    {
        $listId = $this->getListId($title);
        $this->ding2Context->minkContext->visit($this->ding2Context->userPath() . '/dinglists');
        $list = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.user-list a[href="/list/' . $listId . '"]');
        if (!$list) {
            throw new \Exception("Couldn't find list '$title''");
        }
        $list->click();

        $this->ding2Context->minkContext->assertElementContainsText('.ting-object a', $material);
    }

    /**
     * @Then I should not see the material :material on the list :title
     */
    public function iShouldNotSeeTheMaterialOnTheList($material, $title)
    {
        try {
            $this->iShouldSeeTheMaterialOnTheList($material, $title);
            throw new Exception('Material "' . $material . '" not removed from "' . $title . '" list');
        } catch (Exception $e) {
            // Not found, swallow exception.
        }
    }

    /**
     * @When I remove the material :material from the list
     */
    public function iRemoveTheMaterialFromTheList($material)
    {
        $page = $this->ding2Context->minkContext->getSession()->getPage();
        $items = $page->findAll('css', '.ding-type-ding-list-element');
        $removed = false;
        foreach ($items as $item) {
            $title = $item->find('css', '.field-type-ting-title');
            if ($title && (strpos($title->getText(), $material) !== false)) {
                // The remove button has no usable classes, hope it's the
                // right one.
                $button = $item->find('css', 'form #edit-submit');
                if ($button) {
                    $button->click();
                    $removed = true;
                }
            }
        }
        if (!$removed) {
            throw new Exception('Could not find remove button');
        }
    }

    /**
     * @Then I should see the material :material on the public list :title
     */
    public function iShouldSeeTheMaterialOnThePublicList($material, $title)
    {
        $this->iShouldSeeTheMaterialOnTheList($material, $title);

        // Log in as different user and check the list again.
        $listId = $this->getListId($title);
        $this->ding2Context->iAmLoggedInAsALibraryUser();
        $this->ding2Context->minkContext->visit("/list/$listId");
        $this->ding2Context->minkContext->assertElementContainsText('.ting-object', $material);
    }

    /**
     * @Then I should not see the material :material on the public list :title
     */
    public function iShouldNotSeeTheMaterialOnThePublicList($material, $title)
    {
        $listId = $this->getListId($title);
        $this->ding2Context->minkContext->visit("/list/$listId");
        $this->ding2Context->minkContext->assertElementNotContainsText('.ting-object', $material);
    }

    /**
     * @Given I am on the :title list page
     * @When I go to the :title list page
     */
    public function iGoToTheListPage($title)
    {
        $listId = $this->getListId($title);
        $this->ding2Context->minkContext->visitPath("/list/$listId");
    }

    /**
     * @Then I should see the tag :tag on the material
     */
    public function iShouldSeeTheTagOnTheMaterial($tag)
    {
        $this->ding2Context->minkContext->assertElementContainsText('.subjects .subject', $tag);
    }

    /**
     * @Given I have chosen a book material with the tag :tag
     */
    public function iHaveChosenABookMaterialWithTheTag($tag)
    {
        $this->iHaveSearchedFor($tag);
        $link = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '#edit-type-bog');
        if (!$link) {
            throw new Exception("Couldn't filter for book type");
        }
        $link->check();

        // Go to material.
        $this->iChooseTheFirstSearchResult();
    }

    /**
     * @When I choose the first search result
     */
    public function iChooseTheFirstSearchResult()
    {
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.ting-object .heading a');
        if (!$found) {
            throw new Exception("Couldn't find search result.");
        }
        $found->click();
    }

    /**
     * @When I follow the tag :tag
     */
    public function iFollowTheTag($tag)
    {
        // Mouse over the "More" button.
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.ding-list-add-button a');
        if (!$found) {
            throw new \Exception("Couldn't find more button");
        }
        $this->ding2Context->minkContext->getSession()
            ->evaluateScript('jQuery(document).scrollTo(".ding-list-add-button a");');
        $found->mouseOver();

        $followTag = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.buttons a:contains("' . $tag . '")');
        if (!$followTag) {
            throw new Exception("Couldn't find tag '$tag' on material");
        }
        $followTag->click();
    }

    /**
     * @Then I should see the tag :tag on my list :list
     */
    public function iShouldSeeTheTagOnMyList($tag, $list)
    {
        $this->iGoToTheListPage($list);
        $this->ding2Context->minkContext->assertElementContainsText('.vocabulary-ding-content-tags a', $tag);
    }

    /**
     * @Given I am following the tag :tag chosen from the material with collection name :collection
     */
    public function iAmFollowingTheTag($tag, $collection)
    {
        $this->ding2Context->minkContext->visitPath('/ting/collection/' . $collection);
        $this->iFollowTheTag($tag);
        $this->iShouldSeeTheTagOnMyList($tag, 'Mine interesser');
    }

    /**
     * @When I unfollow the tag :tag
     */
    public function iUnfollowTheTag($tag)
    {
        $this->iGoToTheListPage('Mine interesser');

        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', 'a[href^="/tags/"]:contains("' . $tag . '")');
        if (!$found) {
            throw new Exception("Can't unfollow tag '$tag' when it's not being followed");
        }
        $deleteButton = $found->getParent()->getParent()->getParent()
            ->find('css', '.close-btn');
        if (!$deleteButton) {
            throw new Exception("Couldn't find remove from list button");
        }
        $deleteButton->click();
    }

    /**
     * @Then I should not see the tag :arg1 on my list :arg2
     */
    public function iShouldNotSeeTheTagOnMyList($tag, $list)
    {
        $this->iGoToTheListPage($list);
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '.vocabulary-ding-content-tags a:contains("' . $tag . '")');
        if ($found) {
            throw new Exception("Shouldn't find tag '$tag', but it is being followed");
        }
    }

    /**
     * @Given the list :name exists
     */
    public function theListExists($name)
    {
        $listSelectors = [
            'follow search' => '.ding-user-lists .user-searches',
            'follow author' => '.ding-user-lists .follow-author',
            'interests' =>'.ding-user-lists .interests',
            'ratings' => '.ding-user-lists .ratings',
        ];
        if (!isset($listSelectors[$name])) {
            throw new Exception('Unknown list "' . $name . '"');
        }

        $this->ding2Context->drupalContext->visitPath('/user');
        $link = $this->ding2Context->minkContext->getSession()->getPage()->find('css', $listSelectors[$name] . ' a');
        if (!$link) {
            throw new \Exception("Couldn't find the list");
        }
        $list_a = $link->getAttribute('href');
        $match = array();
        if (!preg_match('/\/list\/(\d+)/', $list_a, $match)) {
            throw new \Exception("List link is not formatted correctly");
        }

        // Save id of list.
        $this->dataRegistry['list:' . $name] = $match[1];
    }



    /**
     * @Given I have searched for :search and the tag :tag
     */
    public function iHaveSearchedForAndTheTag($search, $tag)
    {
        $this->iHaveSearchedFor("$search $tag");

        $this->ding2Context->drupalContext->saveScreenshot('screenshot1.png', '/var/www/html/');
        $this->ding2Context->minkContext->getSession()
            ->evaluateScript('jQuery(document).scrollTo(\'#edit-subject a[title="' . $tag . '"]\');');
        $this->ding2Context->drupalContext->saveScreenshot('screenshot2.png', '/var/www/html/');
        $found = $this->ding2Context->minkContext->getSession()->getPage()
            ->find('css', '#edit-subject input[value="' . $tag . '"]');
        if (!$found) {
            throw new Exception("Couldn't filter for tag $tag");
        }
        $found->check();
    }

    /**
     * @Then I should not be able to add material to the list :title as a different user
     */
    public function iShouldNotBeAbleToAddMaterialToTheListAsADifferentUser($title)
    {
        $this->ding2Context->minkContext->assertElementNotOnPage(
            '.buttons li a[href^="/dinglist/attach/ting_object"]:contains("' . $title . '")'
        );
    }

    /**
     * @Given I have searched for :search with the material name :material
     */
    public function iHaveSearchedForWithTheMaterialName($search, $material)
    {
        $this->iHaveSearchedFor($search);
        $this->ding2Context->minkContext->assertElementOnPage('a[href="/ting/collection/' . $material . '"]');
    }

    /**
     * @When I rate the material :material with :stars stars
     * @When I change the rating of material :material to :stars stars
     */
    public function iRateTheMaterialWithStars($material, $stars)
    {
        $page = $this->ding2Context->minkContext->getSession()->getPage();

        $found = $page->find('css', '.ding-rating[data-ding-entity-rating-path^="' . urldecode($material) . '/"]');
        if (!$found) {
            throw new Exception("Couldn't find material '$material'");
        }
        $star = $page->find(
            'css',
            '.ding-rating[data-ding-entity-rating-path^="' . urldecode($material) . '/"]' .
            ' .star:nth-child(' . $stars . ')'
        );
        if (!$star) {
            throw new Exception("Couldn't find star");
        }
        $star->click();

        // Wait for Ajax to finish.
        $page->waitFor(10000, function ($page) {
            return $page->find('css', '.ding-entity-rating-respons');
        });
        $this->ding2Context->minkContext
            ->assertElementContainsText('.ding-entity-rating-respons', 'Tak for din bedømmelse');
    }

    /**
     * @Given I have rated the material :material with :stars stars
     */
    public function iHaveRatedTheMaterialWithStars($material, $stars)
    {
        $this->ding2Context->minkContext->visitPath('/ting/object/' . $material);
        $this->iRateTheMaterialWithStars($material, $stars);
    }

    /**
     * @When I go to the list of rated materials
     */
    public function iGoToTheListOfRatedMaterials()
    {
        $listId = $this->getListId('ratings');
        $this->ding2Context->minkContext->visit("/list/$listId");
    }

    /**
     * @Then I should see that the material :material is marked with :stars stars
     */
    public function iShouldSeeThatTheMaterialIsMarkedWithStars($material, $stars)
    {
        $this->ding2Context->minkContext
            ->assertElementOnPage('.ding-entity-rating[data-ding-entity-rating-path^="' . urldecode($material) . '/"]');
        $this->ding2Context->minkContext->assertNumElements(
            $stars,
            '.ding-entity-rating[data-ding-entity-rating-path^="' . urldecode($material) . '/"]' .
            ' .star.submitted'
        );
    }
}
