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
 * @category   Admin\Content
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Content;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Admin\Form\ContentForm;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Admin\Controller\IndexController;

class ContentController extends IndexController
{
    /**
     * Controller name to which will redirect
     */
    const CONTROLLER_NAME = "content";

    /**
     * Route name to which will redirect
     */
    const ADMIN_ROUTE = "admin";

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/content", "name"=>"Contents"]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Content objects
     */
    public function indexAction()
    {
        $type = (int) $this->getParam("type", 0);
        if ($type === 1) {
            $this->view->contents = $this->getTable("content")->fetchList(false, [], "type='1' AND content.language='".$this->langTranslation."'", null, null,  "content.date DESC");
        }
        if ($type === 0) {
            $this->view->contents = $this->getTable("content")->fetchJoin(false, "menu", [], [], "content.menu=menu.id", "inner", "type='0' AND content.language='".$this->langTranslation."'", null, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Content
     */
    public function addAction()
    {
        $this->showForm('Add', null);
        $this->addBreadcrumb(["reference"=>"/admin/content/add", "name"=>"Add a new content"]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Content object with a given id and session language
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $content = $this->getTable("content")->getContent($this->getParam("id", 0), $this->langTranslation)->current();
        $this->view->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/modify/id/{$content->getId()}", "name"=> $this->translation->MODIFY_CONTENT." &laquo;".$content->getTitle()."&raquo;"]);
        $this->showForm($this->translation->MODIFY, $content);
        return $this->view;
    }

    /**
     * this action deletes a content object with a provided id and session language
     */
    public function deleteAction()
    {
        $content = $this->getTable("content")->deleteContent($this->getParam("id", 0), $this->langTranslation)->current();
        $this->cache->success = "Content was successfully deleted";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
    }

    /**
     * this action shows content details from the provided id and session language
     */
    public function detailAction()
    {
        $content = $this->getTable("content")->getContent($this->getParam("id", 0), $this->langTranslation)->current();
        $this->view->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/detail/id/".$content->getId()."", "name"=>"Content &laquo;". $content->getTitle()."&raquo; details"]);
        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    public function cloneAction()
    {
        $content = $this->getTable("content")->duplicate($this->getParam("id", 0), $this->langTranslation)->current();
        $this->cache->success = "Content &laquo;".$content->getTitle()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/content");
        return $this->view;
    }

    /**
     * This is common function used by add and edit actions
     *
     * @param string $label button title
     * @param  Content|null $menu menu object
     */
    private function showForm($label = 'Add', Content $content = null)
    {
        if (!$content instanceof Content) {
            $content = new Content([], null);
        }

        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "menulink", "parent"], ["language" => $this->langTranslation], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $submenus = $menus = [];
            foreach ($menu as $submenu) {
                if ($submenu->getParent() > 0) {
                    /**
                     * This needs to have a second empty array in order to work
                     */
                    $submenus[$submenu->getParent()][] = $submenu;
                } else {
                    $menus[$submenu->getId()] = $submenu;
                }
            }
        }

        $languages = $this->getTable("Language")->fetchList(false, [], ["active" => 1], "AND", null, "id ASC");
        $form = new ContentForm($content, $menus, $submenus, $languages);
        $form->bind($content);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($content->getInputFilter());
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            if (!empty($data["imageUpload"]) && $this->getRequest()->isXmlHttpRequest()) {
                return $this->uploadImages();
            } else {
                if ($form->isValid()) {
                    $formData = $form->getData();
                    $formData->preview = $content->preview["name"];
                    $this->getTable("content")->saveContent($content);
                    $this->cache->success = "Content &laquo;".$content->getTitle()."&raquo; was successfully saved";
                    $this->view->setTerminal(true);
                    return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
                } else {
                    $this->formErrors($form->getMessages());
                    return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
                }
            }
        }
    }

    /**
     * Get all files from all folders and list them in the gallery
     */
    public function filesAction()
    {
        $this->view->setTerminal(true);
        $path = '/public/userfiles/';
        $dir = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $it  = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $it->setMaxDepth(3);
        $files = [];
        $i = 0;
        foreach ($it as $file) {
            if ($file->isFile()) {
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
     * @return  void
     */
    private function createDirectories()
    {
        if (!is_dir('/public/userfiles/')) {
            mkdir('/public/userfiles/');
        }
        if (!is_dir('/public/userfiles/'.date("Y_M"))) {
            mkdir('/public/userfiles/'.date("Y_M"));
        }
        if (!is_dir('/public/userfiles/'.date("Y_M").'/images/')) {
            mkdir('/public/userfiles/'.date("Y_M").'/images/');
        }
    }

    /**
     * Upload all images async
     *
     * @param  array $files
     * @return Response containing headers with information about each image
     */
    private function uploadImages()
    {
        $this->view->setTerminal(true);
        $adapter = new Http();
        $size = new Size(['min'=>'10kB', 'max'=>'5MB','useByteString' => true]);
        $isImage = new IsImage();
        $extension = new Extension(['jpg','gif','png','jpeg','bmp','webp', 'svg'], true);

        $this->createDirectories();
        $adapter->setValidators([$size, $isImage, $extension]);
        $adapter->setDestination('/public/userfiles/'.date("Y_M").'/images/');
        $this->upload($adapter);
    }

    /**
     * @param  Zend\File\Transfer\Adapter\Http $adapter
     */
    private function upload($adapter)
    {
        $this->view->setTerminal(true);
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
                    $uploadStatus["successFiles"][] = $file["name"]." was uploaded successfully";
                } else {
                    $uploadStatus["errorFiles"][] = $file["name"]. " was not uploaded";
                }
            }
            echo Json::encode($uploadStatus);
            exit;
            // return new JsonModel(["uploadStatus" => Json::encode($uploadStatus)]);
        } else {
            foreach ($adapter->getMessages() as $key => $error) {
                $uploadStatus["errorFiles"][] = $error;
            }
            echo Json::encode($uploadStatus);
            exit;
            // return new JsonModel(["uploadStatus" => Json::encode($uploadStatus)]);
        }
    }
}
