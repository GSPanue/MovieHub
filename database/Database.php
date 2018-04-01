<?php

class Database {
    private $db;
    private $collection;

    function __construct() {
        $this->db = (new MongoDB\Client)->moviehub;
    }

    /**
     * addUser: Adds a user document to the users collection.
     */
    public function addUser($user, $role) {
        $this->db->selectCollection('users')->insertOne([
            'role' => $role,
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'emailAddress' => strtolower($user['emailAddress']),
            'password' => $user['password'],
            'mobileNumber' => $user['mobileNumber'],
            'dateOfBirth' => $user['dateOfBirthDay'] . '-' . $user['dateOfBirthMonth'] . '-' . $user['dateOfBirthYear'],
            'address1' => $user['address1'],
            'address2' => $user['address2'],
            'townOrCity' => $user['townOrCity'],
            'county' => $user['county'],
            'country' => $user['country'],
            'postCode' => $user['postCode']
        ]);
    }

    /**
     * addProduct: Adds a product document to the products collection.
     */
    public function addProduct($query) {
        $this->db->selectCollection('products')->insertOne($query);
    }

    /**
     * removeProduct: Removes a product document from the products collection.
     */
    public function removeProduct($query) {
        $this->setCollection('products');

        $this->collection->deleteOne(
            ['_id' => $this->toObjectID($query['_id'])]
        );
    }

    /**
     * fetchProduct: Returns a single product document or multiple products documents that are
     * sorted by date created in ascending or descending order.
     */
    public function fetchProduct($query, $sort, $order, $limit) {
        $this->setCollection('products');

        if ($sort) {
            return iterator_to_array($this->collection->find(
                [],
                ['sort' => ['dateCreated' => $order], 'limit' => $limit]
            ));
        }
        else {
            return $this->collection->findOne(
                [key($query) => $this->toObjectID($query[key($query)])]
            );
        }
    }

    /**
     * updateProduct: Finds a product document that matches a given ID and sets a field with a new value.
     */
    public function updateProduct($query, $update) {
        $this->setCollection('products');

        $this->collection->updateOne(
            ['_id' => $this->toObjectID($query)],
            ['$set' => $update],
            ['multiple' => true]
        );
    }

    /**
     * fetchUser: Returns a user document from the users collection.
     */
    public function fetchUser($query) {
        $this->setCollection('users');

        return $this->collection->findOne(
            [key($query) => $query[key($query)]]
        );
    }

    /**
     * updateUser: Finds a user document that matches a given ID and sets a field with a new value.
     */
    public function updateUser($query, $update) {
        $this->setCollection('users');

        $this->collection->updateOne(
            ['_id' => $this->toObjectID($query)],
            ['$set' => $update],
            ['multiple' => true]
        );
    }

    /**
     * addOrder: Adds an order document to the orders collection.
     */
    public function addOrder($query) {
        $this->db->selectCollection('orders')->insertOne($query);
    }

    /**
     * fetchOrder: Returns a single order document or multiple order documents that are
     * sorted by date created in ascending or descending order.
     */
    public function fetchOrder($query, $sort, $order, $limit) {
        $this->setCollection('orders');

        if ($sort) {
            return iterator_to_array($this->collection->find(
                ($query === []) ? [] : [key($query) => $this->toObjectID($query[key($query)])],
                ['sort' => ['dateCreated' => $order], 'limit' => $limit]
            ));
        }
        else {
            return $this->collection->findOne(
                [key($query) => $this->toObjectID($query[key($query)])]
            );
        }
    }

    /**
     * updateOrder: Finds a order document that matches a given ID and sets a field with a new value.
     */
    public function updateOrder($query, $update) {
        $this->setCollection('orders');

        $this->collection->updateOne(
            ['_id' => $this->toObjectID($query)],
            ['$set' => $update],
            ['multiple' => true]
        );
    }

    /**
     * addCart: Adds a shopping cart document to the carts collection.
     */
    public function addCart($query) {
        $this->db->selectCollection('carts')->insertOne($query);
    }

    /**
     * fetchCart: Returns a single cart document or multiple cart documents that are
     * sorted by date created in ascending order.
     */
    public function fetchCart($query, $sort) {
        $this->setCollection('carts');

        if ($sort) {
            return iterator_to_array($this->collection->find(
                [],
                ['sort' => ['dateCreated' => 1]]
            ));
        }
        else {
            return $this->collection->findOne(
                [key($query) => $query[key($query)]]
            );
        }
    }

    /**
     * updateCart: Finds a cart document that matches a given ID and either removes it from the
     * carts collection or adds it. $addToSet is used to ensure duplicate products are not stored in
     * the cart. If $set is true, an item field is updated.
     */
    public function updateCart($query, $update, $action, $set) {
        $this->setCollection('carts');

        if ($set) {
            $this->collection->updateOne(
                [key($query) => $query[key($query)]],
                ['$set' => ['items' => $update]]
            );
        }
        else {
            $this->collection->updateOne(
                [key($query) => $query[key($query)]],
                [(($action === 1) ? '$addToSet' : '$pull') => ['items' => $update]]
            );
        }
    }

    /**
     * removeCart: Removes a cart document from the carts collection.
     */
    public function removeCart($query) {
        $this->setCollection('carts');

        $this->collection->deleteOne(
            ['_id' => $this->toObjectID($query['_id'])]
        );
    }

    /**
     * Returns the number of documents in a given collection.
     */
    public function countDocuments($collection) {
        $this->setCollection($collection);

        return $this->collection->count();
    }

    /**
     * Converts a string to a MongoDB Object ID.
     */
    public function toObjectID($string) {
        return new MongoDB\BSON\ObjectID($string);
    }

    /**
     * Sets the collection attribute.
     */
    public function setCollection($collection) {
        $this->collection = $this->db->selectCollection($collection);
    }
}