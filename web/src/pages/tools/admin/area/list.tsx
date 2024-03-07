import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol } from 'tdesign-react/esm'
import { PageTable, ButtonModal, DeleteLink } from '@duxweb/dux-refine'

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
        colKey: 'code',
        title: translate('tools.area.fields.code'),
        ellipsis: true,
      },
      {
        colKey: 'name',
        title: translate('tools.area.fields.name'),
        ellipsis: true,
      },
      {
        colKey: 'level',
        title: translate('tools.area.fields.level'),
        ellipsis: true,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 100,
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

  return (
    <PageTable
      columns={columns}
      table={{
        rowKey: 'id',
      }}
      title={translate('tools.area.name')}
      actionRender={() => (
        <ButtonModal
          title={translate('buttons.import')}
          component={() => import('./import')}
          action='import'
          icon={<div className='t-icon i-tabler:plus'></div>}
        />
      )}
    />
  )
}

export default List
