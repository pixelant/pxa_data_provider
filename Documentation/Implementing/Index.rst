.. include:: ../Includes.txt

.. _implementation:

Implementation
==============


.. _implementation-basics:

Basic Usage
-----------

You can use the extension by calling functions on the
:php:`ConfigurableDataProvider` class or through view helpers in your Fluid
templates.

.. _implementation-basics-configurabledataprovider:

Using the ConfigurableDataProvider
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The :php:`Pixelant\PxaDataProvider\Domain\DataProvider\ConfigurableDataProvider`
class can be used in your own extensions.

It is a singleton instance that defaults to using the TypoScript configuration,
although you can choose to override it by supplying a different configuration
to the constructor.

**Example:** Retrieving an instance of the :php:`ConfigurableDataProvider`
object with default settings from TypoScript:

.. code-block:: php

   $dataProvider = GeneralUtility::makeInstance(
       ConfigurableDataProvider::class
   );

**Example:** Retrieving an instance of the :php:`ConfigurableDataProvider`
object with custom settings:

.. code-block:: php

   $dataProvider = GeneralUtility::makeInstance(
       ConfigurableDataProvider::class,
       $settings
   );

**Retrieving data:** The object has two public functions:

.. code-block:: php

   //Returns an array with data for a single object
   $dataArray = $dataProvider->dataForObject($object);

   //Returns an array with data for multiple objects in an array
   $dataArray = $dataProvider->dataForObject($arrayOfObjects);

Both functions return array data formatted in the same way:

.. code-block:: php

   [
       <key> => [
           [
               <property> => <value>,
               ...
           ]
       ],
       ...
   ]


.. _implementation-basics-viewhelpers:

View Helpers
~~~~~~~~~~~~

You can use the extension's view helpers in your Fluid templates, either inline
or as tags.

Example:

   .. code-block:: html

      <dp:provider.json object="{userObject}" />

Will output the same as:

   .. code-block:: html

      {dp:provider.json(object:userObject)}


.. _implementation-viewhelpers:

Two View Helpers
----------------

There are two view helpers with the same available arguments.


.. _implementation-viewhelpers-array:

provider.array View Helper
~~~~~~~~~~~~~~~~~~~~~~~~~~

This viewhelper outputs a PHP array and can be used for further processing.

   .. code-block:: html

      <f:for each="{dp:provider.array(object:userObject)}" key="objectType" as="objects">
          <h3>{objectType}<h3>
          <f:for each="{objects}" as="object">
              <p>{object.name}</p>
          </f:for>
      </f:for>


.. _implementation-viewhelpers-json:

provider.json View Helper
~~~~~~~~~~~~~~~~~~~~~~~~~~

This viewhelper outputs a JSON array and can be used for transfer to JavaScript.

   .. code-block:: html

      <div data-gtm="{dp:provider.json(object:userObject)}">
         <!-- More HTML -->
      </div>

Will output:

   .. code-block:: html

      <div data-gtm="{&quot;user&quot;:[{&quot;uid&quot;:123,&quot;name&quot;:&quot;John Doe&quot;}]}">
         <!-- More HTML -->
      </div>

