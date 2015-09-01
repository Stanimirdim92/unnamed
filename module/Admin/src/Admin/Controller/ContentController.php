<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.10
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Content;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Admin\Form\ContentForm;
use Zend\File\Transfer\Adapter\Http;

final class ContentController extends IndexController
{
    /**
     * @var ContentForm $contentForm
     */
    private $contentForm = null;

    /**
     * @param ContentForm $contentForm
     */
    public function __construct(ContentForm $contentForm = null)
    {
        parent::__construct();

        $this->contentForm = $contentForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/content", "name"=>$this->translate("CONTENTS")]);
    }

    /**
     * This action shows the list of all contents
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/content/index");
        if ((int) $this->getParam("id", 0) === 1) {
            $this->getView()->contents = $this->getTable("content")->fetchList(false, [], "type='1' AND content.language='".$this->language()."'", null, null,  "content.date DESC");
        } else {
            $this->getView()->contents = $this->getTable("content")->fetchJoin(false, "menu", [], [], "content.menu=menu.id", "inner", "type='0' AND content.language='".$this->language()."'", null, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }
        return $this->getView();
    }

    /**
     * This action serves for adding a new object of type Content
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/content/add");
        $this->initForm($this->translate("ADD_NEW_CONTENT"), null);
        $this->addBreadcrumb(["reference"=>"/admin/content/add", "name"=>$this->translate("ADD_NEW_CONTENT")]);
        return $this->getView();
    }

    /**
     * This action presents a modify form for Content object with a given id and session language
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/content/modify");
        $content = $this->getTable("content")->getContent((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/modify/{$content->getId()}", "name"=> $this->translate("MODIFY_CONTENT")." &laquo;".$content->getTitle()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_CONTENT"), $content);
        return $this->getView();
    }

    /**
     * this action deletes a content
     */
    protected function deleteAction()
    {
        $content = $this->getTable("content")->deleteContent((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_CONTENT_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * this action shows content details
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/content/detail");
        $content = $this->getTable("content")->getContent((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/detail/".$content->getId()."", "name"=>"&laquo;". $content->getTitle()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    protected function deactivateAction()
    {
        $content = $this->getTable("content")->toggleActiveContent((int)$this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("CONTENT_DISABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    protected function activateAction()
    {
        $content = $this->getTable("content")->toggleActiveContent((int)$this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("CONTENT_ENABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * This action will clone the object
     */
    protected function cloneAction()
    {
        $content = $this->getTable("content")->duplicate((int)$this->getParam("id", 0), $this->language())->current();
        $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * This is common function used by add and edit actions
     *
     * @param string $label button title
     * @param  Content|null $content
     */
    private function initForm($label = '', Content $content = null)
    {
        if (!$content instanceof Content) {
            $content = new Content([], null);
        }

        /**
         * @var $form ContentForm
         */
        $form = $this->contentForm;
        $form->bind($content);
        $form->get("submit")->setValue($label);
        $this->getView()->form = $form;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                if (!empty($data["imageUpload"]) && $request->isXmlHttpRequest()) {
                    return $this->uploadImages();
                } else {
                    $formData = $form->getData();
                    /**
                     * We only need the name. All images ar stored in the same folder, based on the month and year
                     */
                    $formData->setPreview($formData->getPreview()["name"]);
                    $this->getTable("content")->saveContent($content);
                    $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
                    return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), "error");
            }
        }
    }

    /**
     * Get all files from all folders and list them in the gallery
     */
    protected function filesAction()
    {
        $this->getView()->setTerminal(true);
        $dir = new \RecursiveDirectoryIterator('public/userfiles/', \FilesystemIterator::SKIP_DOTS);
        $it  = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $it->setMaxDepth(50);
        $files = [];
        $i = 0;
        foreach ($it as $file) {
            if ($file->isFile()) {
                /**
                 * TODO: use pathinfo()
                 */
                $filepath = explode("public", $file->getRealPath()); // ugly workaround :(
                $files[$i]["link"] = Json::encode($filepath[1]);
                $files[$i]["filename"] = Json::encode($file->getFilename());
                $i++;
            }
        }
        return new JsonModel($files);
    }

    /**
     * Create directories
     */
    private function createDirectories()
    {
        // if (!is_dir('public/userfiles/')) {
        //     mkdir('public/userfiles/');
        // }
        // if (!is_dir('public/userfiles/'.date("Y_M"))) {
        //     mkdir('public/userfiles/'.date("Y_M"));
        // }
        if (!is_dir('public/userfiles/'.date("Y_M").'/images/')) {
            mkdir('public/userfiles/'.date("Y_M").'/images/', 0750, true);
        }
    }

    /**
     * Upload all images async
     *
     * @return Response containing headers with information about each image
     */
    private function uploadImages()
    {
        $this->getView()->setTerminal(true);
        $adapter = new Http();

        $this->createDirectories();
        $adapter->setDestination('public/userfiles/'.date("Y_M").'/images/');
        $this->upload($adapter);
    }

    /**
     * @param  Http $adapter
     * @return Json
     */
    private function upload(Http $adapter = null)
    {
        $this->getView()->setTerminal(true);
        $uploadStatus = [];
        if ($adapter->isValid('imageUpload')) {
            foreach ($adapter->getFileInfo() as $key => $file) {
                /**
                 * Skip the normal image upload input
                 */
                if ($key == "preview") {
                    continue;
                }

                if ($adapter->receive($file["name"])) {
                    $uploadStatus["successFiles"][] = $file["name"]." ".$this->translate("UPLOAD_SUCCESS");
                } else {
                    $uploadStatus["errorFiles"][] = $file["name"]." ".$this->translate("UPLOAD_FAIL");
                }
            }
        } else {
            foreach ($adapter->getMessages() as $key => $error) {
                $uploadStatus["errorFiles"][] = $error;
            }
        }
        echo Json::encode($uploadStatus);
        exit;
        // return new JsonModel(["uploadStatus" => Json::encode($uploadStatus)]);
    }
}
