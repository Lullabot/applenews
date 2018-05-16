This module allows Drupal to push nodes to Apple News in the correct
JSON format. Integrates with the 
https://github.com/chapter-three/AppleNewsAPI library.

## Templates

Create Apple News "templates" which tie together a content type with a
layout of Apple News components and the data that is placed into those
components. These are stored as config entities, and so can be defined
in the UI or as YAML in your custom module.

Each component should have its own UUID.

### Template Example

```
uuid: 4650c85e-ec8c-4ebd-a9f5-d13b61622610
langcode: en
status: true
dependencies: {  }
id: test
label: test
node_type: page
columns: 7
width: 1024
margin: 60
gutter: 20
components:
  ea6c4106-88ea-4171-ad5d-8bfd04664c8d:
    uuid: ea6c4106-88ea-4171-ad5d-8bfd04664c8d
    id: 'default_text:author'
    weight: -10
    component_layout:
      column_start: 0
      column_span: null
      margin_top: 0
      margin_bottom: 0
      ignore_margin: none
      ignore_gutter: none
      minimum_height: 10
      minimum_height_unit: points
    component_data:
      text:
        field_name: title
        field_property: base
      format: none
  4f2c21df-d3cf-4bca-85f3-b45f7862c617:
    uuid: 4f2c21df-d3cf-4bca-85f3-b45f7862c617
    id: 'default_image:photo'
    weight: -9
    component_layout:
      column_start: 0
      column_span: null
      margin_top: 0
      margin_bottom: 0
    component_data:
      URL:
        field_name: title
        field_property: base
      caption:
        field_name: title
        field_property: base
```

## Components

The module comes with a set of default components as defined by the 
Apple News documentation. Each one is mapped to a Component class from
https://github.com/chapter-three/AppleNewsAPI

Each component has a "meta-type" that defines what it predominantly
displays. Currently, there are 4 types:

- text
- image
- nested
- divider

These are mainly used to determine which normalizer should be used during
serialization. You can define your own "meta-type" by using it in a
custom ComponentType annotation (see below) and by adding the appropriate
schema.

Here is the schema for the text type, as an example:

```
applenews.component_type.text:
  type: mapping
  mapping:
    text:
      type: applenews.field_mapping
    format:
      type: string
      label: 'Format for included text (none, html, or markdown)'
```

You can define your own Apple News component option by putting a class 
in Plugin/applenews/ComponentType, extending ApplenewsComponentTypeBase,
and using the correct annotation.

```
@ApplenewsComponentType(
 id = "your_component_id",
 label = @Translation("Your component label"),
 description = @Translation("Your component description"),
 component_type = "image",
)
```

## Normalizers

The module makes use of Drupal's Serialization API by defining several
custom normalizers. These are applicable with the format "applenews". 

Overriding a normalizer is another way your module can provide additional
customization. In your *.services.yml file, declare your normalizer
service and give it a priority higher than the one your are trying to
override from applenews.services.yml.

It is recommended, though not required, to have your normalizer class
extend one of the Apple News base normalizers.
