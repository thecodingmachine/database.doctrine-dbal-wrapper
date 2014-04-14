#Doctrine DBAL wrapper classes for Mouf


> > ##WARNING! IN DEVELOPMENT! NOT READY FOR PRODUCTION

This package provides a wrapper around the Doctrine's DBALConnection class.

When installed, it provides a user-friendly user interface that allows to create / edit your connection to the database.

![DBCOnnection](doc/images/db_connection_install.png "Install DBAL/Connection")

Important: this component has not been tested yet, for other connection drivers than PDO_MYSQL and MSQLI.

Please be aware this is a very simple wrapper. In fact this component is just a simple install task, that will automatically configure a Doctrine/DBAL Connection :

 * the **params** property is stored as a PHP field as it's structure is not really defined. It will return an array of connection parameters : database's host, name, and identifiers.
   
   Please note, that those parameters will be stored in Mouf configuration.
 * **driver** & **eventManager** are simple classes that has no settable properties. Note that as required by Doctrine, if the ORM layer is included in your project, the entityManager instance will be associated to the same eventManager instance that the connection. 
 * the **configuration** property is not set by this package.
 
Other parameters can be defined after the instance has been initialized, please refer to doctrine's documentation for more information : [http://doctrine-dbal.readthedocs.org/en/latest/](http://doctrine-dbal.readthedocs.org/en/latest/).