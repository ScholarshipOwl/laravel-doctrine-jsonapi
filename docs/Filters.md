# Filtering
The package provides "filter" param implementation, so you could simply implement the filters in the list action

[Filtering JSON:API Specs](https://jsonapi.org/format/#fetching-filtering)

## Search filter
[SearchFilterParser](../src/FilterParsers/SearchFilterParser.php)

You need this filter when you need to search like filter, other words the `LIKE` where condition.

You need to set the property that will be filtered by `setFilterProperty` method on List action.
For example `setFilterProperty('name')`.
In this case if query param `filter=q` provided condition `WHERE alias.name LIKE ('%q%')`.

## Array filter
[ArrayFilterParser](../src/FilterParsers/ArrayFilterParser.php)

This filter allows to send more multiple filters per field and more complicated fields.

### Equal Filter
TODO:


