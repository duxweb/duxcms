import React, { useRef } from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Switch } from 'tdesign-react/esm'
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
              <Modal
                title={translate('buttons.edit')}
                trigger={<Link theme='primary'>{translate('buttons.edit')}</Link>}
                component={() => import('./save')}
                componentProps={{ id: row.id }}
              />
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'system.role',
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
      title={translate('system.api.name')}
      actionRender={() => (
        <Modal
          title={translate('buttons.create')}
          trigger={
            <Button icon={<div className='t-icon i-tabler:plus'></div>}>
              {translate('buttons.create')}
            </Button>
          }
          component={() => import('./save')}
        ></Modal>
      )}
    />
  )
}

export default List
