import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol } from 'tdesign-react/esm'
import { DeleteLink, PageTable } from '@duxweb/dux-refine'

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
        title: translate('content.tags.fields.name'),
        ellipsis: true,
      },
      {
        colKey: 'view',
        title: translate('content.tags.fields.view'),
        ellipsis: true,
      },
      {
        colKey: 'count',
        title: translate('content.tags.fields.count'),
        ellipsis: true,
      },
      {
        colKey: 'created_at',
        title: translate('content.tags.fields.createdAt'),
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
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return <PageTable columns={columns} />
}

export default List
