<?php

/**
 * @file plugins/generic/gwjPdfViewer/GwJPdfViewerPlugin.php
 *
 * GWJ Modern PDF Viewer Plugin
 */

namespace APP\plugins\generic\gwjPdfViewer;

use APP\core\Application;
use APP\template\TemplateManager;
use Exception;
use PKP\plugins\Hook;

class GwJPdfViewerPlugin extends \PKP\plugins\GenericPlugin
{
    /**
     * Register the plugin.
     */
    public function register($category, $path, $mainContextId = null)
    {
        if (parent::register($category, $path, $mainContextId)) {

            if ($this->getEnabled($mainContextId)) {

                Hook::add('PreprintHandler::view::galley', $this->submissionCallback(...), Hook::SEQUENCE_LAST);
                Hook::add('ArticleHandler::view::galley', $this->submissionCallback(...), Hook::SEQUENCE_LAST);
                Hook::add('IssueHandler::view::galley', $this->issueCallback(...), Hook::SEQUENCE_LAST);
            }

            return true;
        }

        return false;
    }

    public function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    public function getDisplayName()
    {
        return __('plugins.generic.gwjPdfViewer.name');
    }

    public function getDescription()
    {
        return __('plugins.generic.gwjPdfViewer.description');
    }

    /**
     * Render article/preprint PDF galley.
     */
    public function submissionCallback($hookName, $args)
    {
        $request = &$args[0];
        $application = Application::get();

        switch ($application->getName()) {

            case 'ojs2':
                $issue = &$args[1];
                $galley = &$args[2];
                $submission = &$args[3];
                $submissionNoun = 'article';
                break;

            case 'ops':
                $galley = &$args[1];
                $submission = &$args[2];
                $submissionNoun = 'preprint';
                $issue = null;
                break;

            default:
                throw new Exception('Unknown application!');
        }

        if ($galley && $galley->getFileType() === 'application/pdf') {

            $galleyPublication = null;

            foreach ($submission->getData('publications') as $publication) {
                if ($publication->getId() === $galley->getData('publicationId')) {
                    $galleyPublication = $publication;
                    break;
                }
            }

            $templateMgr = TemplateManager::getManager($request);

            // 🔥 Registrar CSS externo del plugin
            $templateMgr->addStyleSheet(
                'gwjPdfViewerStyles',
                $request->getBaseUrl() . '/' . $this->getPluginPath() . '/templates/display.css',
                ['contexts' => ['frontend']]
            );

            if ($galleyPublication) {
                $title = $galleyPublication->getLocalizedTitle(null, 'html');
            }

            $pdfUrl = $request->url(
                null,
                $submissionNoun,
                'download',
                [
                    $submission->getBestId(),
                    $galley->getBestGalleyId(),
                    $galley->getFile()->getId()
                ]
            );

            $parentUrl = $request->url(
                null,
                $submissionNoun,
                'view',
                [$submission->getBestId()]
            );

            $galleyTitle = __('submission.representationOfTitle', [
                'representation' => $galley->getLabel(),
                'title' => $galleyPublication->getLocalizedFullTitle(),
            ]);

            $datePublished = __('submission.outdatedVersion', [
                'datePublished' => $galleyPublication->getData('datePublished'),
                'urlRecentVersion' => $parentUrl,
            ]);

            $templateMgr->assign([
                'pluginUrl' => $request->getBaseUrl() . '/' . $this->getPluginPath(),
                'issue' => $issue,
                'title' => $title,
                'pdfUrl' => $pdfUrl,
                'parentUrl' => $parentUrl,
                'galleyTitle' => $galleyTitle,
                'datePublished' => $datePublished,
                'isLatestPublication' => $submission->getData('currentPublicationId') === $galley->getData('publicationId'),
                'isTitleHtml' => true,
            ]);

            $templateMgr->display($this->getTemplateResource('display.tpl'));
            return true;
        }

        return false;
    }

    /**
     * Render issue PDF galley.
     */
    public function issueCallback($hookName, $args)
    {
        $request = &$args[0];
        $issue = &$args[1];
        $galley = &$args[2];

        if ($galley && $galley->getFileType() === 'application/pdf') {

            $templateMgr = TemplateManager::getManager($request);

            $templateMgr->addStyleSheet(
                'gwjPdfViewerStyles',
                $request->getBaseUrl() . '/' . $this->getPluginPath() . '/templates/display.css',
                ['contexts' => ['frontend']]
            );

            $pdfUrl = $request->url(
                null,
                'issue',
                'download',
                [
                    $issue->getBestIssueId(),
                    $galley->getBestGalleyId()
                ]
            );

            $parentUrl = $request->url(
                null,
                'issue',
                'view',
                [$issue->getBestIssueId()]
            );

            $galleyTitle = __('submission.representationOfTitle', [
                'representation' => $galley->getLabel(),
                'title' => $issue->getIssueIdentification(),
            ]);

            $datePublished = __('submission.outdatedVersion', [
                'datePublished' => $issue->getData('datePublished'),
                'urlRecentVersion' => $parentUrl,
            ]);

            $templateMgr->assign([
                'pluginUrl' => $request->getBaseUrl() . '/' . $this->getPluginPath(),
                'title' => $issue->getIssueIdentification(),
                'pdfUrl' => $pdfUrl,
                'parentUrl' => $parentUrl,
                'galleyTitle' => $galleyTitle,
                'datePublished' => $datePublished,
                'isLatestPublication' => true,
                'isTitleHtml' => false,
            ]);

            $templateMgr->display($this->getTemplateResource('display.tpl'));
            return true;
        }

        return false;
    }
}
