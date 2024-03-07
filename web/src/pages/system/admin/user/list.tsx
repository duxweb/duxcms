import React from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Switch, Input } from 'tdesign-react/esm'
import {
  PageTable,
  MediaText,
  FilterItem,
  CreateButtonModal,
  EditLinkModal,
  DeleteLink,
} from '@duxweb/dux-refine'

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
      actionRender={() => <CreateButtonModal component={() => import('./save')} />}
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
