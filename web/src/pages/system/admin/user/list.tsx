import React from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Switch, Input } from 'tdesign-react/esm'
import { PageTable, Modal, MediaText, FilterItem } from '@duxweb/dux-refine'

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
        colKey: 'nickname',
        title: translate('system.user.fields.nickname'),
        ellipsis: true,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Avatar image={row.avatar}>{row.nickname[0]}</MediaText.Avatar>
              <MediaText.Title>{row.nickname}</MediaText.Title>
            </MediaText>
          )
        },
      },
      {
        colKey: 'username',
        title: translate('system.user.fields.username'),
        ellipsis: true,
      },
      {
        colKey: 'status',
        title: translate('system.user.fields.status'),
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
                    resource: 'system.user',
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
    [translate],
  )

  return (
    <PageTable
      columns={columns}
      table={{
        rowKey: 'id',
      }}
      title={translate('system.user.name')}
      tabs={[
        {
          label: translate('system.user.tabs.all'),
          value: '0',
        },
        {
          label: translate('system.user.tabs.enabled'),
          value: '1',
        },
        {
          label: translate('system.user.tabs.disabled'),
          value: '2',
        },
      ]}
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
      filterRender={() => {
        return (
          <>
            <FilterItem name='keyword'>
              <Input />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
