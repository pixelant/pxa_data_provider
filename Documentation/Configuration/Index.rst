.. include:: ../Includes.txt

.. _configuration:

Configuration
=============


.. _configuration-overview:

Overview
--------

The configuration is made for each class property. You must specify the full
class path, and then configure which properties should be output.

Classes and configurations of their output properties are configured in
:typoscript:`plugin.tx_pxadataprovider.objectConfig`. In this example, we're
configuring :php:`TYPO3\CMS\Extbase\DomainObject\AbstractEntity`.

   .. code-block:: typoscript

      plugin.tx_pxadataprovider {
        settings {
          objectConfig {
            TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
              key = entity
              includeProperties = uid,pid,_timestamp
              excludeProperties = pid
              remapProperties {
                uid = id
              }

              processProperties {
                _timestamp {
                  data = date : c
                }
              }
            }
          }
        }
      }


.. _configuration-classinheritance:

Class inheritance
-----------------

Configurations for one class is inherited to the class' children. In the
example above, the configuration will be applied to every descendant class
of :php:`TYPO3\CMS\Extbase\DomainObject\AbstractEntity`, such as
:php:`TYPO3\CMS\Extbase\Domain\Model\FrontendUser` or really any entity class
within an Extbase extension.

You can override configurations by specifying configurations for a descendant.
Take this configuration as an example:

   .. code-block:: typoscript

       objectConfig {
         TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
           key = entity
           includeProperties = uid
         }
         TYPO3\CMS\Extbase\Domain\Model\FrontendUser {
           key = user
           includeProperties = name
         }
       }

When outputting :php:`TYPO3\CMS\Extbase\Domain\Model\FrontendUser` object data,
the configuration used will be

   .. code-block:: typoscript

        key = user
        includeProperties = uid,name

As you can see, the :typoscript:`includeProperties` property includes both `uid`
and `name` properties.


.. _configuration-whatisaproperty:

What is a Property?
-------------------

You will often think of properties as being database fields, but they are
really any class property accessible through a :php:`$object->getProperty()` or
:php:`$object->isProperty()` call.

In the example above, the data will be retrieved by calling

   * For `name`: :php:`$object->getName()` (if it exists) or alternately
     :php:`$object->isName()` (if it exists).

   * For `name`: :php:`$object->getUid()` (if it exists) or alternately
     :php:`$object->isName()` (if it exists).

If neither :php:`$object->getProperty()` nor
:php:`$object->isProperty()` exists, the value will be returned as :php:`null`.


.. _configuration-configurationoptions:

Class Configuration Options
---------------------------

The options are listed in the order of execution.


.. _configuration-configurationoptions-key:

key
~~~

:aspect:`Property`
   key

:aspect:`Data type`
   string

:aspect:`Description`
   The name used for the container of output data for objects of this class.

:aspect:`Example`

   .. code-block:: typoscript

      TYPO3\CMS\Extbase\Domain\Model\FrontendUser {
        key = user
      }

   If the object :html:`{userObject}` is of class
   :php:`TYPO3\CMS\Extbase\Domain\Model\FrontendUser` or a descendant class,
   this Fluid template:

   .. code-block:: html

      <dp:provider.json object="{userObject}" />

   Will output JSON (formatting added):

   .. code-block:: javascript

      {
         "user": [
            {
               "uid": 123,
               "name": "John Doe"
            }
         ]
      }


.. _configuration-configurationoptions-includeproperties:

includeProperties
~~~~~~~~~~~~~~~~~

:aspect:`Property`
   includeProperties

:aspect:`Data type`
   string (comma-separated values)

:aspect:`Description`
   The properties to be output.

:aspect:`Example`

   .. code-block:: typoscript

      includeProperties = uid,name


.. _configuration-configurationoptions-excludeproperties:

excludeProperties
~~~~~~~~~~~~~~~~~

:aspect:`Property`
   excludeProperties

:aspect:`Data type`
   string (comma-separated values)

:aspect:`Description`
   Properties explicitly to be removed from output.

:aspect:`Example`

   .. code-block:: typoscript

      excludeProperties = password,passwordHint


.. _configuration-configurationoptions-remapproperties:

remapProperties
~~~~~~~~~~~~~~~

:aspect:`Property`
   remapProperties

:aspect:`Data type`
   Array of properties and their new names

:aspect:`Description`
   Properties explicitly to be removed from output.

:aspect:`Example`

   This will output the value of the property `uid` under the property name
   `id`.

   .. code-block:: typoscript

      remapProperties {
        uid = id
      }


.. _configuration-configurationoptions-processproperties:

processProperties
~~~~~~~~~~~~~~~~~

:aspect:`Property`
   processProperties

:aspect:`Data type`
   Array of property names and :ref:`t3tsref:stdWrap` processing instructions.

:aspect:`Description`
   Use :ref:`t3tsref:stdWrap` to modify property values before output. You
   can also use this configuration to generate new properties

:aspect:`Example`

   This will output the value of the property `uid` under the property name
   `id`.

   .. code-block:: typoscript

      remapProperties {
        name {
          wrap = <name>|</name>
        }
        _timestamp {
         data = date : c
        }
      }

   Will output JSON (formatting added):

   .. code-block:: javascript

      {
         "user": [
            {
               "name": "<name>John Doe</name>",
               "_timestamp": "2020-01-24T12:40:51+00:00"
            }
         ]
      }


