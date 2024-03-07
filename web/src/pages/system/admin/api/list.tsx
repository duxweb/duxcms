import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Switch } from 'tdesign-react/esm'
import { PageTable, CreateButtonModal, EditLinkModal, DeleteLink } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 150,
      },
      {
        colKey: 'secret_id',
        title: translate('system.api.fields.secretId'),
        ellipsis: true,
      },
      {
        colKey: 'secret_key',
        title: translate('system.api.fields.secretKey'),
        ellipsis: true,
      },
      {
        colKey: 'status',
        title: translate('system.api.fields.status'),
        edit: {
          component: Switch,
          props: {},
          keepEditMode: true,
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
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
      table={{
        rowKey: 'id',
      }}
      title={translate('system.api.name')}
      actionRender={() => <CreateButtonModal component={() => import('./save')} />}
    />
  )
}

export default List
