import React from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Tag } from 'tdesign-react/esm'
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
        colKey: 'name',
        title: translate('sms.tpl.fields.name'),
        ellipsis: true,
      },
      {
        colKey: 'method',
        title: translate('sms.tpl.fields.method'),
        ellipsis: true,
      },
      {
        colKey: 'type',
        title: translate('sms.tpl.fields.type'),
        cell: ({ row }) => {
          return (
            <>
              {!row.type ? (
                <Tag theme='warning' variant='outline'>
                  {translate('sms.tpl.fields.typeTpl')}
                </Tag>
              ) : (
                <Tag theme='success' variant='outline'>
                  {translate('sms.tpl.fields.typeContent')}
                </Tag>
              )}
            </>
          )
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
                    resource: 'sms.tpl',
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
      title={translate('sms.tpl.name')}
      actionRender={() => (
        <Modal
          title={translate('buttons.create')}
          trigger={
            <Button icon={<div className='i-tabler:plus t-icon'></div>}>
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
