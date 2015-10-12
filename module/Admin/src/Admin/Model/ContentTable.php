<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Model\AbstractModelTable;
use Admin\Exception\RuntimeException;

final class ContentTable extends AbstractModelTable
{
    /**
     * @method __construct
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('content', 'Content', $adapter);
    }

    /**
     * @param int $id content id
     * @param int $language user language
     *
     * @throws RuntimeException If content is not found
     *
     * @return Content
     */
    public function getContent($id = 0, $language = 1)
    {
        $rowset = $this->select(['id' => (int) $id, "language" => (int) $language]);
        $rowset->buffer();
        if (!$rowset->current()) {
            throw new RuntimeException("Couldn't find content");
        }
        return $rowset->current();
    }

    /**
     * Delete content based on the provided id and language.
     *
     * @param int $id content id
     * @param int $language user language
     *
     * @return Content
     */
    public function deleteContent($id = 0, $language = 1)
    {
        if ($this->getContent($id, $language)) {
            $this->delete(['id' => (int) $id, "language" => (int) $language]);
        }
    }

    /**
     * Save or update content based on the provided id and language.
     *
     * @param  Content $content
     *
     * @return Content
     */
    public function saveContent(Content $content)
    {
        $data = [
            'menu'      => (int) $content->getMenu(),
            'title'     => (string) $content->getTitle(),
            'preview'   => (string) $content->getPreview(),
            'text'      => (string) $content->getText(),
            'menuOrder' => (int) $content->getMenuOrder(),
            'type'      => (int) $content->getType(),
            'date'      => (string) $content->getDate(),
            'language'  => (int) $content->getLanguage(),
            'titleLink' => (string) $content->getTitleLink(),
            'active'    => (int) $content->getActive(),
            'author'    => (string) $content->getAuthor(),
        ];
        $id = (int) $content->getId();
        $language = (int) $content->getLanguage();
        if (!$id) {
            $this->insert($data);
        } else {
            if ($this->getContent($id, $language)) {
                $this->update($data, ['id' => $id, 'language' => $language]);
            }
        }
        unset($id, $language, $data);
        return $content;
    }

    /**
     * This method can disable or enable contents.
     *
     * @param int $id content id
     * @param  int $state 0 - deactivated, 1 - active
     */
    public function toggleActiveContent($id = 0, $state = 0)
    {
        if ($this->getContent($id)) {
            $this->update(["active" => (int) $state], ['id' => (int) $id]);
        }
    }

    /**
     * duplicate a content.
     *
     * @param  int    $id
     * @param  int    $language
     *
     * @return Content
     */
    public function duplicate($id = 0, $language = 1)
    {
        $content = $this->getContent($id, $language);
        $clone = $content->getCopy();
        $this->saveContent($clone);
        return $clone;
    }
}
