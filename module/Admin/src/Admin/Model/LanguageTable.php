<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;
use Admin\Entity\Language;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

final class LanguageTable
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Doctrine\ORM\QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @param Query|QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param boolean            $fetchJoinCollection Whether the query joins a collection (true by default).
     *
     * @return Paginator
     */
    public function preparePagination($query, $fetchJoinCollection = true)
    {
        return new ZendPaginator(new PaginatorAdapter(new ORMPaginator($query, $fetchJoinCollection)));
    }

    /**
     * @return Admin\Entity\Language
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Language");
    }

    /**
     * @param int $id
     *
     * @throws RuntimeException
     *
     * @return Language
     */
    public function getLanguage($id = 0)
    {
        $language = $this->getEntityRepository()->find($id);

        if (empty($language)) {
            throw new RuntimeException("Couldn't find language");
        }

        return $language;
    }

    /**
     * @param int $id
     *
     * @return Language
     */
    public function deleteLanguage($id = 0)
    {
        $language = $this->getLanguage($id);
        if ($language) {
            $this->entityManager->remove($language);
            $this->entityManager->flush();
        }
    }

    /**
     * Save or update language based on the provided id.
     *
     * @param Language $language
     *
     * @return Language
     */
    public function saveLanguage(Language $language)
    {
        $this->entityManager->persist($language);
        $this->entityManager->flush();

        return $language;
    }
}
