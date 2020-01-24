.. include:: ../Includes.txt

.. _introduction:

Introduction
============


.. _introduction-whatdoesitdo:

What Does it Do?
----------------

This extension is mainly accessed through Fluid view helpers. It outputs data
from supplied objects according to (and limited by) the TypoScript
configuration. Only configured fields will be output, meaning that you can
control data output centrally and you are safe from accidentally outputting
restricted information.

The output properties can be modified or checked using :ref:`t3tsref:stdWrap`.
This way, the output can also be sanitized or changed to accomodate the
recipient.


.. _introduction-usecases:

Example Use Cases
-----------------

   * Transferring structured data to external tools, such as Google Tag
     Manager.

   * Making entity objects available to JavaScript without revealing more
     properties than necessary.

   * Outputting object properties sanitized or encoded to be usable by an
     external processor.
