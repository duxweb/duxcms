import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol } from 'tdesign-react/esm'
import { CreateButtonModal, DeleteLink, EditLinkModal, PageTable } from '@duxweb/dux-refine'

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
        title: translate('content.source.fields.name'),
        ellipsis: true,
      },
      {
        colKey: 'created_at',
        title: translate('content.source.fields.createdAt'),
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
              <EditLinkModal rowId={row.id} component={() => import('./save')} />
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return (
    <PageTable
      columns={columns}
      actionRender={() => <CreateButtonModal component={() => import('./save')} />}
    />
  )
}

export default List
