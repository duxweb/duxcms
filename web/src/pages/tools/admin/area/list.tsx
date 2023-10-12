import React, { useRef } from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm } from 'tdesign-react/esm'
import { PageTable, Modal } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()

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
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'tools.area',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate]
  )

  return (
    <PageTable
      columns={columns}
      table={{
        rowKey: 'id',
      }}
      title={translate('tools.area.name')}
      actionRender={() => (
        <Modal
          title={translate('buttons.import')}
          trigger={
            <Button icon={<div className='i-tabler:plus t-icon'></div>}>
              {translate('buttons.import')}
            </Button>
          }
          component={() => import('./import')}
        ></Modal>
      )}
    />
  )
}

export default List
