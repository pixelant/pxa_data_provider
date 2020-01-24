.. include:: ../Includes.txt

.. _implementation:

Implementation
==============


.. _implementation-basics:

Basic Usage
-----------

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

