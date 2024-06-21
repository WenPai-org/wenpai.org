<?php 
/**
	Admin Page Framework v3.8.34 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/index-wp-mysql-for-speed>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class Imfs_AdminPageFramework_Router extends Imfs_AdminPageFramework_Factory {
    public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'index-wp-mysql-for-speed') {
        $_sPropertyClassName = isset($this->aSubClassNames['oProp']) ? $this->aSubClassNames['oProp'] : 'Imfs_AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, $sCallerPath, get_class($this), $sOptionKey, $sCapability, $sTextDomain);
        parent::__construct($this->oProp);
        if (!$this->oProp->bIsAdmin) {
            return;
        }
        add_action('wp_loaded', array($this, '_replyToDetermineToLoad'));
        add_action('set_up_' . $this->oProp->sClassName, array($this, '_replyToLoadComponentsForAjax'), 100);
    }
    public function _replyToLoadComponentsForAjax() {
        if (!$this->oProp->bIsAdminAjax) {
            return;
        }
        new Imfs_AdminPageFramework_Model_Menu__RegisterMenu($this, 'pseudo_admin_menu');
        do_action('pseudo_admin_menu', '');
        do_action('pseudo_current_screen');
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        if ($this->oProp->isPageAdded($_sPageSlug)) {
            do_action("pseudo_current_screen_{$_sPageSlug}");
        }
    }
    protected function _getLinkObject() {
        $_sClassName = $this->aSubClassNames['oLink'];
        return new $_sClassName($this->oProp, $this->oMsg);
    }
    protected function _getPageLoadObject() {
        $_sClassName = $this->aSubClassNames['oPageLoadInfo'];
        return new $_sClassName($this->oProp, $this->oMsg);
    }
    public function __call($sMethodName, $aArgs = null) {
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        $_sTabSlug = $this->oProp->getCurrentTabSlug($_sPageSlug);
        $_mFirstArg = $this->oUtil->getElement($aArgs, 0);
        $_aKnownMethodPrefixes = array('section_pre_', 'field_pre_', 'load_pre_',);
        switch ($this->_getCallbackName($sMethodName, $_sPageSlug, $_aKnownMethodPrefixes)) {
            case 'section_pre_':
                return $this->_renderSectionDescription($sMethodName);
            case 'field_pre_':
                return $this->_renderSettingField($_mFirstArg, $_sPageSlug);
            case 'load_pre_':
                return $this->_doPageLoadCall($sMethodName, $_sPageSlug, $_sTabSlug, $_mFirstArg);
            default:
                return parent::__call($sMethodName, $aArgs);
        }
    }
    private function _getCallbackName($sMethodName, $sPageSlug, array $aKnownMethodPrefixes = array()) {
        foreach ($aKnownMethodPrefixes as $_sMethodPrefix) {
            if ($this->oUtil->hasPrefix($_sMethodPrefix, $sMethodName)) {
                return $_sMethodPrefix;
            }
        }
        return '';
    }
    protected function _doPageLoadCall($sMethodName, $sPageSlug, $sTabSlug, $oScreen) {
        if (!$this->_isPageLoadCall($sMethodName, $sPageSlug, $oScreen)) {
            return;
        }
        $this->_setPageAndTabSlugsForForm($sPageSlug, $sTabSlug);
        $this->_setShowDebugInfoProperty($sPageSlug);
        $this->_load(array("load_{$this->oProp->sClassName}", "load_{$sPageSlug}",));
        $sTabSlug = $this->oProp->getCurrentTabSlug($sPageSlug);
        if (strlen($sTabSlug)) {
            $this->_setShowDebugInfoProperty($sPageSlug, $sTabSlug);
            $this->oUtil->addAndDoActions($this, array("load_{$sPageSlug}_" . $sTabSlug), $this);
            add_filter('admin_title', array($this, '_replyToSetAdminPageTitleForTab'), 1, 2);
        }
        $this->oUtil->addAndDoActions($this, array("load_after_{$this->oProp->sClassName}", "load_after_{$sPageSlug}",), $this);
    }
    private function _setShowDebugInfoProperty($sPageSlug, $sTabSlug = '') {
        if (!strlen($sTabSlug)) {
            $this->oProp->bShowDebugInfo = $this->oUtil->getElement($this->oProp->aPages, array($sPageSlug, 'show_debug_info'), $this->oProp->bShowDebugInfo);
            return;
        }
        $this->oProp->bShowDebugInfo = $this->oUtil->getElement($this->oProp->aInPageTabs, array($sPageSlug, $sTabSlug, 'show_debug_info'), $this->oProp->bShowDebugInfo);
    }
    private function _setPageAndTabSlugsForForm($sPageSlug, $sTabSlug) {
        $this->oForm->aSections['_default']['page_slug'] = $sPageSlug ? $sPageSlug : null;
        $this->oForm->aSections['_default']['tab_slug'] = $sTabSlug ? $sTabSlug : null;
    }
    private function _isPageLoadCall($sMethodName, $sPageSlug, $osScreenORPageHook) {
        if (substr($sMethodName, strlen('load_pre_')) !== $sPageSlug) {
            return false;
        }
        if (!isset($this->oProp->aPageHooks[$sPageSlug])) {
            return false;
        }
        $_sPageHook = is_object($osScreenORPageHook) ? $osScreenORPageHook->id : $sPageSlug;
        return $_sPageHook === $this->oProp->aPageHooks[$sPageSlug];
    }
    protected function _isInstantiatable() {
        if ($this->_isWordPressCoreAjaxRequest()) {
            return false;
        }
        return !is_network_admin();
    }
    protected function _isInThePage() {
        if (!$this->oProp->bIsAdmin) {
            return false;
        }
        if (!did_action('set_up_' . $this->oProp->sClassName)) {
            return true;
        }
        return $this->oProp->isPageAdded();
    }
    public function _replyToLoadComponents() {
        if ('plugins.php' === $this->oProp->sPageNow) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();
        }
        parent::_replyToLoadComponents();
    }
    }
    abstract class Imfs_AdminPageFramework_Model_Form extends Imfs_AdminPageFramework_Router {
        public $aFieldErrors;
        protected $_sTargetPageSlug = null;
        protected $_sTargetTabSlug = null;
        protected $_sTargetSectionTabSlug = null;
        public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'index-wp-mysql-for-speed') {
            parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
            if (!$this->oProp->bIsAdmin) {
                return;
            }
            if (isset($_REQUEST['apf_remote_request_test']) && '_testing' === $_REQUEST['apf_remote_request_test']) {
                exit('OK');
            }
        }
        public function _replyToHandleSubmittedFormData($aSavedData, $aArguments, $aSectionsets, $aFieldsets) {
            new Imfs_AdminPageFramework_Model__FormSubmission($this, $aSavedData, $aArguments, $aSectionsets, $aFieldsets);
        }
        public function _replyToFieldsetResourceRegistration($aFieldset) {
            $aFieldset = $aFieldset + array('help' => null, 'title' => null, 'help_aside' => null, 'page_slug' => null, 'tab_slug' => null, 'section_title' => null, 'section_id' => null,);
            if (!$aFieldset['help']) {
                return;
            }
            $_sRootSectionID = $this->oUtil->getElement($this->oUtil->getAsArray($aFieldset['section_id']), 0);
            $this->addHelpTab(array('page_slug' => $aFieldset['page_slug'], 'page_tab_slug' => $aFieldset['tab_slug'], 'help_tab_title' => $aFieldset['section_title'], 'help_tab_id' => $_sRootSectionID, 'help_tab_content' => "<span class='contextual-help-tab-title'>" . $aFieldset['title'] . "</span> - " . PHP_EOL . $aFieldset['help'], 'help_tab_sidebar_content' => $aFieldset['help_aside'] ? $aFieldset['help_aside'] : "",));
        }
        public function _replyToModifySectionsets($aSectionsets) {
            $this->_registerHelpPaneItemsOfFormSections($aSectionsets);
            return parent::_replyToModifySectionsets($aSectionsets);
        }
        public function _registerHelpPaneItemsOfFormSections($aSectionsets) {
            foreach ($aSectionsets as $_aSectionset) {
                $_aSectionset = $_aSectionset + array('help' => null, 'page_slug' => null, 'tab_slug' => null, 'title' => null, 'section_id' => null, 'help' => null, 'help_aside' => null,);
                if (empty($_aSectionset['help'])) {
                    continue;
                }
                $this->addHelpTab(array('page_slug' => $_aSectionset['page_slug'], 'page_tab_slug' => $_aSectionset['tab_slug'], 'help_tab_title' => $_aSectionset['title'], 'help_tab_id' => $_aSectionset['section_id'], 'help_tab_content' => $_aSectionset['help'], 'help_tab_sidebar_content' => $this->oUtil->getElement($_aSectionset, 'help_aside', ''),));
            }
        }
        public function _replyToDetermineSectionsetVisibility($bVisible, $aSectionset) {
            if (!current_user_can($aSectionset['capability'])) {
                return false;
            }
            if (!$aSectionset['if']) {
                return false;
            }
            if (!$this->_isSectionOfCurrentPage($aSectionset)) {
                return false;
            }
            return $bVisible;
        }
        private function _isSectionOfCurrentPage(array $aSectionset) {
            $_sCurrentPageSlug = ( string )$this->oProp->getCurrentPageSlug();
            if ($aSectionset['page_slug'] !== $_sCurrentPageSlug) {
                return false;
            }
            if (!$aSectionset['tab_slug']) {
                return true;
            }
            return ($aSectionset['tab_slug'] === $this->oProp->getCurrentTabSlug($_sCurrentPageSlug));
        }
        public function _replyToDetermineFieldsetVisibility($bVisible, $aFieldset) {
            $_sCurrentPageSlug = $this->oProp->getCurrentPageSlug();
            if ($aFieldset['page_slug'] !== $_sCurrentPageSlug) {
                return false;
            }
            return parent::_replyToDetermineFieldsetVisibility($bVisible, $aFieldset);
        }
        public function _replyToFormatFieldsetDefinition($aFieldset, $aSectionsets) {
            if (empty($aFieldset)) {
                return $aFieldset;
            }
            $_aSectionPath = $this->oUtil->getAsArray($aFieldset['section_id']);
            $_sSectionPath = implode('|', $_aSectionPath);
            $aFieldset['option_key'] = $this->oProp->sOptionKey;
            $aFieldset['class_name'] = $this->oProp->sClassName;
            $aFieldset['page_slug'] = $this->oUtil->getElement($aSectionsets, array($_sSectionPath, 'page_slug'), $this->oProp->getCurrentPageSlugIfAdded());
            $aFieldset['tab_slug'] = $this->oUtil->getElement($aSectionsets, array($_sSectionPath, 'tab_slug'), $this->oProp->getCurrentInPageTabSlugIfAdded());
            $_aSectionset = $this->oUtil->getElementAsArray($aSectionsets, $_sSectionPath);
            $aFieldset['section_title'] = $this->oUtil->getElement($_aSectionset, 'title');
            $aFieldset['capability'] = $aFieldset['capability'] ? $aFieldset['capability'] : $this->_replyToGetCapabilityForForm($this->oUtil->getElement($_aSectionset, 'capability'), $_aSectionset['page_slug'], $_aSectionset['tab_slug']);
            return parent::_replyToFormatFieldsetDefinition($aFieldset, $aSectionsets);
        }
        public function _replyToFormatSectionsetDefinition($aSectionset) {
            if (empty($aSectionset)) {
                return $aSectionset;
            }
            $aSectionset = $aSectionset + array('page_slug' => null, 'tab_slug' => null, 'capability' => null,);
            $aSectionset['page_slug'] = $this->_getSectionPageSlug($aSectionset);
            $aSectionset['tab_slug'] = $this->_getSectionTabSlug($aSectionset);
            $aSectionset['capability'] = $this->_getSectionCapability($aSectionset);
            return parent::_replyToFormatSectionsetDefinition($aSectionset);
        }
        private function _getSectionCapability($aSectionset) {
            if ($aSectionset['capability']) {
                return $aSectionset['capability'];
            }
            if (0 < $aSectionset['_nested_depth']) {
                $_aSectionPath = $aSectionset['_section_path_array'];
                array_pop($_aSectionPath);
                $_sParentCapability = $this->oUtil->getElement($this->oForm->aSectionsets, array_merge($_aSectionPath, array('capability')));
                if ($_sParentCapability) {
                    return $_sParentCapability;
                }
            }
            return $this->_replyToGetCapabilityForForm($aSectionset['capability'], $aSectionset['page_slug'], $aSectionset['tab_slug']);
        }
        private function _getSectionPageSlug($aSectionset) {
            if ($aSectionset['page_slug']) {
                return $aSectionset['page_slug'];
            }
            if (0 < $aSectionset['_nested_depth']) {
                $_aSectionPath = $aSectionset['_section_path_array'];
                $_sRootSectionID = $this->oUtil->getFirstElement($_aSectionPath);
                $_sRootSectionPageSlug = $this->oUtil->getElement($this->oForm->aSectionsets, array($_sRootSectionID, 'page_slug'));
                if ($_sRootSectionPageSlug) {
                    return $_sRootSectionPageSlug;
                }
            }
            return $this->oProp->getCurrentPageSlugIfAdded();
        }
        private function _getSectionTabSlug($aSectionset) {
            if ($aSectionset['tab_slug']) {
                return $aSectionset['tab_slug'];
            }
            return $this->oProp->getCurrentInPageTabSlugIfAdded();
        }
        public function _replyToDetermineWhetherToProcessFormRegistration($bAllowed) {
            if ($this->oProp->bIsAdminAjax) {
                return true;
            }
            $_sPageSlug = $this->oProp->getCurrentPageSlug();
            return $this->oProp->isPageAdded($_sPageSlug);
        }
        public function _replyToGetCapabilityForForm($sCapability) {
            $_aParameters = func_get_args() + array('', '', '');
            $_sPageSlug = $this->oUtil->getAOrB($_aParameters[1], $_aParameters[1], $this->oProp->getCurrentPageSlug());
            $_sTabSlug = $this->oUtil->getAOrB($_aParameters[2], $_aParameters[2], $this->oProp->getCurrentTabSlug($_sPageSlug));
            $_sTabCapability = $this->_getInPageTabCapability($_sTabSlug, $_sPageSlug);
            $_sPageCapability = $this->_getPageCapability($_sPageSlug);
            $_aCapabilities = array_values(array_filter(array($_sTabCapability, $_sPageCapability))) + array($this->oProp->sCapability);
            return $_aCapabilities[0];
        }
    }
    abstract class Imfs_AdminPageFramework_View_Form extends Imfs_AdminPageFramework_Model_Form {
        public function _replyToGetSectionName() {
            $_aParams = func_get_args() + array(null, null,);
            $sNameAttribute = $_aParams[0];
            $aSectionset = $_aParams[1];
            $_aSectionPath = $aSectionset['_section_path_array'];
            $_aDimensionalKeys = array($this->oProp->sOptionKey);
            foreach ($_aSectionPath as $_sDimension) {
                $_aDimensionalKeys[] = '[' . $_sDimension . ']';
            }
            if (isset($aSectionset['_index'])) {
                $_aDimensionalKeys[] = '[' . $aSectionset['_index'] . ']';
            }
            return implode('', $_aDimensionalKeys);
        }
        public function _replyToGetFieldNameAttribute() {
            $_aParams = func_get_args() + array(null, null,);
            $sNameAttribute = $_aParams[0];
            $aFieldset = $_aParams[1];
            $_aDimensionalKeys = array($this->oProp->sOptionKey);
            if ($this->isSectionSet($aFieldset)) {
                $_aSectionPath = $aFieldset['_section_path_array'];
                foreach ($_aSectionPath as $_sDimension) {
                    $_aDimensionalKeys[] = '[' . $_sDimension . ']';
                }
                if (isset($aFieldset['_section_index'])) {
                    $_aDimensionalKeys[] = '[' . $aFieldset['_section_index'] . ']';
                }
            }
            foreach ($aFieldset['_field_path_array'] as $_sPathPart) {
                $_aDimensionalKeys[] = '[' . $_sPathPart . ']';
            }
            return implode('', $_aDimensionalKeys);
        }
        public function _replyToGetFlatFieldName() {
            $_aParams = func_get_args() + array(null, null,);
            $sNameAttribute = $_aParams[0];
            $aFieldset = $_aParams[1];
            $_aDimensionalKeys = array($this->oProp->sOptionKey);
            if ($this->isSectionSet($aFieldset)) {
                foreach ($aFieldset['_section_path_array'] as $_sDimension) {
                    $_aDimensionalKeys[] = $_sDimension;
                }
                if (isset($aFieldset['_section_index'])) {
                    $_aDimensionalKeys[] = $aFieldset['_section_index'];
                }
            }
            $_aDimensionalKeys = array_merge($_aDimensionalKeys, $aFieldset['_field_path_array']);
            return implode('|', $_aDimensionalKeys);
        }
        public function _replyToGetInputNameAttribute() {
            $_aParams = func_get_args() + array(null, null, null);
            $sNameAttribute = $_aParams[0];
            $aField = $_aParams[1];
            $sKey = ( string )$_aParams[2];
            $sKey = $this->oUtil->getAOrB('0' !== $sKey && empty($sKey), '', "[{$sKey}]");
            $_sNamePrefix = $this->_replyToGetFieldNameAttribute('', $aField);
            return $_sNamePrefix . $sKey;
        }
        public function _replyToGetFlatInputName() {
            $_aParams = func_get_args() + array(null, null, null);
            $sFlatNameAttribute = $_aParams[0];
            $aField = $_aParams[1];
            $_sKey = ( string )$_aParams[2];
            $_sKey = $this->oUtil->getAOrB('0' !== $_sKey && empty($_sKey), '', "|" . $_sKey);
            return $this->_replyToGetFlatFieldName('', $aField) . $_sKey;
        }
    }
    abstract class Imfs_AdminPageFramework_Controller_Form extends Imfs_AdminPageFramework_View_Form {
        public function addSettingSections() {
            foreach (func_get_args() as $asSection) {
                $this->addSettingSection($asSection);
            }
            $this->_sTargetTabSlug = null;
            $this->_sTargetSectionTabSlug = null;
        }
        public function addSettingSection($asSection) {
            if (!is_array($asSection)) {
                $this->_sTargetPageSlug = is_string($asSection) ? $asSection : $this->_sTargetPageSlug;
                return;
            }
            $aSection = $asSection;
            $this->_sTargetPageSlug = $this->_getTargetPageSlug($aSection);
            $this->_sTargetTabSlug = $this->_getTargetTabSlug($aSection);
            $this->_sTargetSectionTabSlug = $this->oUtil->getElement($aSection, 'section_tab_slug', $this->_sTargetSectionTabSlug);
            $aSection = $this->oUtil->uniteArrays($aSection, array('page_slug' => $this->_sTargetPageSlug, 'tab_slug' => $this->_sTargetTabSlug, 'section_tab_slug' => $this->_sTargetSectionTabSlug,));
            $aSection['section_tab_slug'] = $this->oUtil->sanitizeSlug($aSection['section_tab_slug']);
            if (!$aSection['page_slug']) {
                return;
            }
            $this->oForm->addSection($aSection);
        }
        private function _getTargetPageSlug($aSection) {
            $_sTargetPageSlug = $this->oUtil->getElement($aSection, 'page_slug', $this->_sTargetPageSlug);
            $_sTargetPageSlug = $_sTargetPageSlug ? $this->oUtil->sanitizeSlug($_sTargetPageSlug) : $this->oProp->getCurrentPageSlugIfAdded();
            return $_sTargetPageSlug;
        }
        private function _getTargetTabSlug($aSection) {
            $_sTargetTabSlug = $this->oUtil->getElement($aSection, 'tab_slug', $this->_sTargetTabSlug);
            $_sTargetTabSlug = $_sTargetTabSlug ? $this->oUtil->sanitizeSlug($aSection['tab_slug']) : $this->oProp->getCurrentInPageTabSlugIfAdded();
            return $_sTargetTabSlug;
        }
        public function removeSettingSections() {
            foreach (func_get_args() as $_sSectionID) {
                $this->oForm->removeSection($_sSectionID);
            }
        }
        public function addSettingFields() {
            foreach (func_get_args() as $aField) {
                $this->addSettingField($aField);
            }
        }
        public function addSettingField($asField) {
            $this->oForm->addField($asField);
        }
        public function removeSettingFields($sFieldID1, $sFieldID2 = null, $_and_more = null) {
            foreach (func_get_args() as $_sFieldID) {
                $this->oForm->removeField($_sFieldID);
            }
        }
        public function getValue() {
            $_aParams = func_get_args();
            $_aDimensionalKeys = $_aParams + array(null, null);
            $_mDefault = null;
            if (is_array($_aDimensionalKeys[0])) {
                $_mDefault = $_aDimensionalKeys[1];
                $_aDimensionalKeys = $_aDimensionalKeys[0];
            }
            return Imfs_AdminPageFramework_WPUtility::getOption($this->oProp->sOptionKey, empty($_aParams) ? null : $_aDimensionalKeys, $_mDefault, $this->getSavedOptions() + $this->oForm->getDefaultFormValues());
        }
        public function getFieldValue($sFieldID, $sSectionID = '') {
            $this->oUtil->showDeprecationNotice('The method,' . __METHOD__ . ',', 'getValue()');
            $_aOptions = $this->oUtil->uniteArrays($this->oProp->aOptions, $this->oForm->getDefaultFormValues());
            if (!$sSectionID) {
                if (array_key_exists($sFieldID, $_aOptions)) {
                    return $_aOptions[$sFieldID];
                }
                foreach ($_aOptions as $aOptions) {
                    if (array_key_exists($sFieldID, $aOptions)) {
                        return $aOptions[$sFieldID];
                    }
                }
            }
            if ($sSectionID) {
                if (array_key_exists($sSectionID, $_aOptions) && array_key_exists($sFieldID, $_aOptions[$sSectionID])) {
                    return $_aOptions[$sSectionID][$sFieldID];
                }
            }
            return null;
        }
    }
    abstract class Imfs_AdminPageFramework_Model_Page extends Imfs_AdminPageFramework_Controller_Form {
        public function _replyToFinalizeInPageTabs() {
            if (!$this->oProp->isPageAdded()) {
                return;
            }
            foreach ($this->oProp->aPages as $_sPageSlug => $_aPage) {
                if (!isset($this->oProp->aInPageTabs[$_sPageSlug])) {
                    continue;
                }
                $_oFormatter = new Imfs_AdminPageFramework_Format_InPageTabs($this->oProp->aInPageTabs[$_sPageSlug], $_sPageSlug, $this);
                $this->oProp->aInPageTabs[$_sPageSlug] = $_oFormatter->get();
                $this->oProp->aDefaultInPageTabs[$_sPageSlug] = $this->_getDefaultInPageTab($_sPageSlug, $this->oProp->aInPageTabs[$_sPageSlug]);
            }
        }
        protected function _finalizeInPageTabs() {
            $this->_replyToFinalizeInPageTabs();
        }
        private function _getDefaultInPageTab($sPageSlug, $aInPageTabs) {
            foreach ($aInPageTabs as $_aInPageTab) {
                if (!isset($_aInPageTab['tab_slug'])) {
                    continue;
                }
                return $_aInPageTab['tab_slug'];
            }
        }
        public function _getPageCapability($sPageSlug) {
            return $this->oUtil->getElement($this->oProp->aPages, array($sPageSlug, 'capability'));
        }
        public function _getInPageTabCapability($sTabSlug, $sPageSlug) {
            return $this->oUtil->getElement($this->oProp->aInPageTabs, array($sPageSlug, $sTabSlug, 'capability'));
        }
    }
    abstract class Imfs_AdminPageFramework_View_Page extends Imfs_AdminPageFramework_Model_Page {
        public function _replyToSetAdminPageTitleForTab($sAdminTitle, $sTitle) {
            $_sTabTitle = $this->oUtil->getElement($this->oProp->aInPageTabs, array($this->oProp->getCurrentPageSlug(), $this->oProp->getCurrentTabSlug(), 'title'));
            if (!$_sTabTitle) {
                return $sAdminTitle;
            }
            return $_sTabTitle . ' &lsaquo; ' . $sAdminTitle;
        }
        public function _replyToEnablePageMetaBoxes() {
            new Imfs_AdminPageFramework_View__PageMetaboxEnabler($this);
        }
        public function _replyToEnqueuePageAssets() {
            new Imfs_AdminPageFramework_View__Resource($this);
        }
        public function _replyToRenderPage() {
            $_sPageSlug = $this->oProp->getCurrentPageSlug();
            $this->_renderPage($_sPageSlug, $this->oProp->getCurrentTabSlug($_sPageSlug));
        }
        protected function _renderPage($sPageSlug, $sTabSlug = null) {
            $_oPageRenderer = new Imfs_AdminPageFramework_View__PageRenderer($this, $sPageSlug, $sTabSlug);
            $_oPageRenderer->render();
        }
    }
    abstract class Imfs_AdminPageFramework_Controller_Page extends Imfs_AdminPageFramework_View_Page {
        public function addInPageTabs() {
            foreach (func_get_args() as $asTab) {
                $this->addInPageTab($asTab);
            }
        }
        public function addInPageTab($asInPageTab) {
            static $__sTargetPageSlug;
            if (!is_array($asInPageTab)) {
                $__sTargetPageSlug = is_string($asInPageTab) ? $asInPageTab : $__sTargetPageSlug;
                return;
            }
            $aInPageTab = $asInPageTab + array('page_slug' => $__sTargetPageSlug, 'tab_slug' => null, 'order' => null,);
            $__sTargetPageSlug = $aInPageTab['page_slug'];
            if (!isset($aInPageTab['page_slug'], $aInPageTab['tab_slug'])) {
                return;
            }
            $_aElements = $this->oUtil->getElement($this->oProp->aInPageTabs, $aInPageTab['page_slug'], array());
            $_iCountElement = count($_aElements);
            $aInPageTab = array('page_slug' => $this->oUtil->sanitizeSlug($aInPageTab['page_slug']), 'tab_slug' => $this->oUtil->sanitizeSlug($aInPageTab['tab_slug']), 'order' => $this->oUtil->getAOrB(is_numeric($aInPageTab['order']), $aInPageTab['order'], $_iCountElement + 10),) + $aInPageTab;
            $this->oProp->aInPageTabs[$aInPageTab['page_slug']][$aInPageTab['tab_slug']] = $aInPageTab;
        }
        public function setPageTitleVisibility($bShow = true, $sPageSlug = '') {
            $this->_setPageProperty('bShowPageTitle', 'show_page_title', $bShow, $sPageSlug);
        }
        public function setPageHeadingTabsVisibility($bShow = true, $sPageSlug = '') {
            $this->_setPageProperty('bShowPageHeadingTabs', 'show_page_heading_tabs', $bShow, $sPageSlug);
        }
        public function setInPageTabsVisibility($bShow = true, $sPageSlug = '') {
            $this->_setPageProperty('bShowInPageTabs', 'show_in_page_tabs', $bShow, $sPageSlug);
        }
        public function setInPageTabTag($sTag = 'h3', $sPageSlug = '') {
            $this->_setPageProperty('sInPageTabTag', 'in_page_tab_tag', $sTag, $sPageSlug);
        }
        public function setPageHeadingTabTag($sTag = 'h2', $sPageSlug = '') {
            $this->_setPageProperty('sPageHeadingTabTag', 'page_heading_tab_tag', $sTag, $sPageSlug);
        }
        private function _setPageProperty($sPropertyName, $sPropertyKey, $mValue, $sPageSlug) {
            $sPageSlug = $this->oUtil->sanitizeSlug($sPageSlug);
            if ($sPageSlug) {
                $this->oProp->aPages[$sPageSlug][$sPropertyKey] = $mValue;
                return;
            }
            $this->oProp->{$sPropertyName} = $mValue;
            foreach ($this->oProp->aPages as & $_aPage) {
                $_aPage[$sPropertyKey] = $mValue;
            }
        }
    }
    abstract class Imfs_AdminPageFramework_Model_Menu extends Imfs_AdminPageFramework_Controller_Page {
        public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'index-wp-mysql-for-speed') {
            parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
            new Imfs_AdminPageFramework_Model_Menu__RegisterMenu($this);
        }
    }
    abstract class Imfs_AdminPageFramework_View_Menu extends Imfs_AdminPageFramework_Model_Menu {
    }
    abstract class Imfs_AdminPageFramework_Controller_Menu extends Imfs_AdminPageFramework_View_Menu {
        protected $_aBuiltInRootMenuSlugs = array('dashboard' => 'index.php', 'posts' => 'edit.php', 'media' => 'upload.php', 'links' => 'link-manager.php', 'pages' => 'edit.php?post_type=page', 'comments' => 'edit-comments.php', 'appearance' => 'themes.php', 'plugins' => 'plugins.php', 'users' => 'users.php', 'tools' => 'tools.php', 'settings' => 'options-general.php', 'network admin' => "network_admin_menu",);
        public function setRootMenuPage($sRootMenuLabel, $sIcon16x16 = null, $iMenuPosition = null) {
            $sRootMenuLabel = trim($sRootMenuLabel);
            $_sSlug = $this->_isBuiltInMenuItem($sRootMenuLabel);
            $this->oProp->aRootMenu = array('sTitle' => $sRootMenuLabel, 'sPageSlug' => $_sSlug ? $_sSlug : $this->oProp->sClassName, 'sIcon16x16' => $this->oUtil->getResolvedSRC($sIcon16x16), 'iPosition' => $iMenuPosition, 'fCreateRoot' => empty($_sSlug),);
        }
        private function _isBuiltInMenuItem($sMenuLabel) {
            $_sMenuLabelLower = strtolower($sMenuLabel);
            if (array_key_exists($_sMenuLabelLower, $this->_aBuiltInRootMenuSlugs)) {
                return $this->_aBuiltInRootMenuSlugs[$_sMenuLabelLower];
            }
        }
        public function setRootMenuPageBySlug($sRootMenuSlug) {
            $this->oProp->aRootMenu['sPageSlug'] = $sRootMenuSlug;
            $this->oProp->aRootMenu['fCreateRoot'] = false;
        }
        public function addSubMenuItems() {
            foreach (func_get_args() as $_aSubMenuItem) {
                $this->addSubMenuItem($_aSubMenuItem);
            }
        }
        public function addSubMenuItem(array $aSubMenuItem) {
            if (isset($aSubMenuItem['href'])) {
                $this->addSubMenuLink($aSubMenuItem);
            } else {
                $this->addSubMenuPage($aSubMenuItem);
            }
        }
        public function addSubMenuLink(array $aSubMenuLink) {
            if (!isset($aSubMenuLink['href'], $aSubMenuLink['title'])) {
                return;
            }
            if (!filter_var($aSubMenuLink['href'], FILTER_VALIDATE_URL)) {
                return;
            }
            $_oFormatter = new Imfs_AdminPageFramework_Format_SubMenuLink($aSubMenuLink, $this, count($this->oProp->aPages) + 1);
            $_aSubMenuLink = $_oFormatter->get();
            $this->oProp->aPages[$_aSubMenuLink['href']] = $_aSubMenuLink;
        }
        public function addSubMenuPages() {
            foreach (func_get_args() as $_aSubMenuPage) {
                $this->addSubMenuPage($_aSubMenuPage);
            }
        }
        public function addSubMenuPage(array $aSubMenuPage) {
            if (!isset($aSubMenuPage['page_slug'])) {
                return;
            }
            $_oFormatter = new Imfs_AdminPageFramework_Format_SubMenuPage($aSubMenuPage, $this, count($this->oProp->aPages) + 1);
            $_aSubMenuPage = $_oFormatter->get();
            $this->oProp->aPages[$_aSubMenuPage['page_slug']] = $_aSubMenuPage;
        }
    }
    abstract class Imfs_AdminPageFramework_Model extends Imfs_AdminPageFramework_Controller_Menu {
    }
    abstract class Imfs_AdminPageFramework_View extends Imfs_AdminPageFramework_Model {
        public function content($sContent) {
            return $sContent;
        }
    }
    abstract class Imfs_AdminPageFramework_Controller extends Imfs_AdminPageFramework_View {
        public function load() {
        }
        public function setUp() {
        }
        public function addHelpTab($aHelpTab) {
            $this->oHelpPane->_addHelpTab($aHelpTab);
        }
        public function enqueueStyles() {
            $_aParams = func_get_args() + array(array(), '', '', array());
            return $this->oResource->_enqueueResourcesByType($_aParams[0], array('sPageSlug' => $_aParams[1], 'sTabSlug' => $_aParams[2],) + $_aParams[3], 'style');
        }
        public function enqueueStyle() {
            $_aParams = func_get_args() + array('', '', '', array());
            return $this->oResource->_addEnqueuingResourceByType($_aParams[0], array('sPageSlug' => $_aParams[1], 'sTabSlug' => $_aParams[2],) + $_aParams[3], 'style');
        }
        public function enqueueScripts() {
            $_aParams = func_get_args() + array(array(), '', '', array());
            return $this->oResource->_enqueueResourcesByType($_aParams[0], array('sPageSlug' => $_aParams[1], 'sTabSlug' => $_aParams[2],) + $_aParams[3], 'script');
        }
        public function enqueueScript() {
            $_aParams = func_get_args() + array('', '', '', array());
            return $this->oResource->_addEnqueuingResourceByType($_aParams[0], array('sPageSlug' => $_aParams[1], 'sTabSlug' => $_aParams[2],) + $_aParams[3], 'script');
        }
        public function addLinkToPluginDescription($sTaggedLinkHTML1, $sTaggedLinkHTML2 = null, $_and_more = null) {
            if ('plugins.php' !== $this->oProp->sPageNow) {
                return;
            }
            $this->oLink->_addLinkToPluginDescription(func_get_args());
        }
        public function addLinkToPluginTitle($sTaggedLinkHTML1, $sTaggedLinkHTML2 = null, $_and_more = null) {
            if ('plugins.php' !== $this->oProp->sPageNow) {
                return;
            }
            $this->oLink->_addLinkToPluginTitle(func_get_args());
        }
        public function setPluginSettingsLinkLabel($sLabel) {
            $this->oProp->sLabelPluginSettingsLink = $sLabel;
        }
        public function setCapability($sCapability) {
            $this->oProp->sCapability = $sCapability;
            if (isset($this->oForm)) {
                $this->oForm->sCapability = $sCapability;
            }
        }
        public function setAdminNotice($sMessage, $sClassSelector = 'error', $sID = '') {
            $sID = $sID ? $sID : md5($sMessage);
            $this->oProp->aAdminNotices[$sID] = array('sMessage' => $sMessage, 'aAttributes' => array('id' => $sID, 'class' => $sClassSelector));
            new Imfs_AdminPageFramework_AdminNotice($this->oProp->aAdminNotices[$sID]['sMessage'], $this->oProp->aAdminNotices[$sID]['aAttributes'], array('should_show' => array($this, 'isInThePage'),));
        }
        public function setDisallowedQueryKeys($asQueryKeys, $bAppend = true) {
            if (!$bAppend) {
                $this->oProp->aDisallowedQueryKeys = ( array )$asQueryKeys;
                return;
            }
            $aNewQueryKeys = array_merge(( array )$asQueryKeys, $this->oProp->aDisallowedQueryKeys);
            $aNewQueryKeys = array_filter($aNewQueryKeys);
            $aNewQueryKeys = array_unique($aNewQueryKeys);
            $this->oProp->aDisallowedQueryKeys = $aNewQueryKeys;
        }
        static public function getOption($sOptionKey, $asKey = null, $vDefault = null) {
            return Imfs_AdminPageFramework_WPUtility::getOption($sOptionKey, $asKey, $vDefault);
        }
    }
    abstract class Imfs_AdminPageFramework extends Imfs_AdminPageFramework_Controller {
        protected $_sStructureType = 'admin_page';
        public function __construct($isOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'index-wp-mysql-for-speed') {
            if (!$this->_isInstantiatable()) {
                return;
            }
            parent::__construct($isOptionKey, $this->_getCallerPath($sCallerPath), $sCapability, $sTextDomain);
        }
        private function _getCallerPath($sCallerPath) {
            if ($sCallerPath) {
                return trim($sCallerPath);
            }
            if (!is_admin()) {
                return null;
            }
            if (!isset($GLOBALS['pagenow'])) {
                return null;
            }
            return 'plugins.php' === $GLOBALS['pagenow'] || isset($_GET['page']) ? Imfs_AdminPageFramework_Utility::getCallerScriptPath(__FILE__) : null;
        }
    }
    