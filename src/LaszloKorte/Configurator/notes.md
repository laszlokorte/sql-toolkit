Annotations
===========


Table
-----
@Title('Some custom Title')
@Description('Explanation for this table...')
@Display('{{someColumn}} {{someRel}} {{otherRel.foreignColumn}}')
@Visible(yes/no)
@Parent('some_rel')
@Sort('sort')

@CollectionView('Grid')
@CollectionView('Map',['long','lat'])
@CollectionView('Calendar','created_at')

@SyntheticInterface('location',['long','lat'])

Column
------
@Title('Title of the column')
@Description('Explanation for this column')
@Visible(yes/no)
@Aggregatable()

@Interface()