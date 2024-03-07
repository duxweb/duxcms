import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Link } from 'tdesign-react/esm'
import { PageTable, ButtonModal, DeleteLink, useDownload } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()

  const { download } = useDownload()

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
        colKey: 'name',
        title: translate('tools.backup.fields.name'),
        ellipsis: true,
      },
      {
        colKey: 'created_at',
        title: translate('tools.backup.fields.createdAt'),
        sorter: true,
        sortType: 'all',
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 150,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link
                theme='primary'
                onClick={() => {
                  download(`tools/backup/download/${row.id}`)
                }}
              >
                下载
              </Link>
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
        <>
          <ButtonModal
            title={translate('buttons.import')}
            component={() => import('./import')}
            action='import'
            icon={<div className='t-icon i-tabler:database-import'></div>}
          />

          <ButtonModal
            title={translate('buttons.export')}
            component={() => import('./export')}
            action='export'
            theme='primary'
            variant='outline'
            icon={<div className='t-icon i-tabler:database-export'></div>}
          />
        </>
      )}
    />
  )
}

export default List
