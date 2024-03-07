import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol } from 'tdesign-react/esm'
import { CreateButton, DeleteLink, EditLink, PageTable } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'name',
        title: translate('content.recommend.fields.name'),
        ellipsis: true,
      },
      {
        colKey: 'articles',
        title: translate('content.recommend.fields.articles'),
        ellipsis: true,
        cell: ({ row }) => {
          return row?.articles?.length
        },
      },
      {
        colKey: 'created_at',
        title: translate('content.recommend.fields.createdAt'),
        sorter: true,
        sortType: 'all',
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <EditLink rowId={row.id} />
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return <PageTable columns={columns} actionRender={() => <CreateButton />} />
}

export default List
