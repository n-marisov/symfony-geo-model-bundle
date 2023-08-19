### Модели для географии.

### Таблицы БД.
- `coordinates` => В таблице хранятся все координаты.
- `multi_coordinates` => В таблице хранятся коллекции точек (MultiPoint, LineString, Polygon).
- `multi_coordinates_union` => Таблица объединения для `coordinates` и `multi_coordinates`.
- `multi_figure` => В таблице хранятся коллекции LineString или Polygon.
- `multi_figure_union`  => Таблица объединения для `multi_coordinates` и `multi_figure`